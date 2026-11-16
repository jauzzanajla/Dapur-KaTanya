<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PickupSchedule;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display product catalog for customers.
     */
    public function index(Request $request): View
    {
        $category = $request->query('category');
        $search = $request->query('search');

        $query = Product::with(['images' => function ($q) {
            $q->orderBy('sort_order', 'asc');
        }])->where('is_active', true);

        if ($category && in_array($category, ['makanan', 'minuman', 'paket'])) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        $products = $query->orderBy('id', 'asc')->get();
        $featuredProduct = Product::with('images')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->first();
        $pickupSchedules = $this->activePickupSchedules();

        $qrisPath = null;
        $publicDisk = config('filesystems.disks.public', []);
        if (($publicDisk['driver'] ?? null) === 's3' && (empty($publicDisk['key']) || empty($publicDisk['secret']))) {
            Log::warning('QRIS lookup skipped because S3 credentials are not configured.');
        } else {
            try {
                $qrisPath = collect(['png', 'jpg', 'jpeg'])
                    ->map(fn (string $extension): string => "qris-pembayaran.{$extension}")
                    ->first(fn (string $path): bool => Storage::disk('public')->exists($path));
            } catch (\Throwable $exception) {
                Log::warning('QRIS storage lookup failed while loading the customer catalog.', [
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        return view('customer.catalog', compact('products', 'category', 'search', 'qrisPath', 'featuredProduct', 'pickupSchedules'));
    }

    /**
     * Store a new customer order.
     */
    public function storeOrder(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => [
                'required',
                'string',
                'min:10',
                'max:20',
                'regex:/^(\+62|62|0)[0-9]{9,15}$/',
            ],
            'pickup_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:'.now('Asia/Jakarta')->toDateString(),
                Rule::exists('pickup_schedules', 'pickup_date')->where('is_active', true),
            ],
            'pickup_time' => 'nullable|string|max:50',
            'pickup_location' => 'required|in:Kampus B,Kampus C,Lainnya',
            'pickup_location_other' => 'required_if:pickup_location,Lainnya|nullable|string|max:150',
            'notes' => 'nullable|string|max:500',
            'payment_proof' => 'required|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
            'cart_items' => 'required|string', // JSON string from client cart
        ], [
            'customer_phone.required' => 'Nomor HP Asli WAJIB diisi sebelum melanjutkan checkout!',
            'customer_phone.regex' => 'Format nomor HP tidak valid. Masukkan nomor HP aktif (misal: 08123456789).',
            'pickup_date.required' => 'Tanggal pengambilan pesanan (Pickup) wajib dipilih.',
            'pickup_date.after_or_equal' => 'Tanggal pickup tidak boleh tanggal yang sudah lewat.',
            'pickup_date.exists' => 'Tanggal pickup sudah tidak tersedia. Silakan pilih jadwal PO yang masih aktif.',
            'pickup_location.required' => 'Lokasi pengambilan (Pickup) wajib dipilih.',
            'pickup_location.in' => 'Pilih Kampus B, Kampus C, atau opsi lainnya.',
            'pickup_location_other.required_if' => 'Tuliskan lokasi pickup jika memilih Lainnya.',
            'payment_proof.required' => 'Bukti transfer QRIS WAJIB diunggah sebelum pesanan dikonfirmasi.',
            'payment_proof.max' => 'Ukuran file bukti transfer maksimal 5MB.',
            'cart_items.required' => 'Keranjang belanja Anda masih kosong.',
        ]);

        $rawCart = json_decode($request->input('cart_items'), true);
        if (empty($rawCart) || ! is_array($rawCart)) {
            return back()->withErrors(['cart_items' => 'Keranjang belanja tidak boleh kosong.'])->withInput();
        }

        $pickupLocation = $request->input('pickup_location') === 'Lainnya'
            ? trim($request->input('pickup_location_other'))
            : $request->input('pickup_location');

        // Upload payment proof
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $filename = 'proof_'.date('Ymd_His').'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $proofPath = $file->storeAs('payment_proofs', $filename, 'public');
        }

        DB::beginTransaction();
        try {
            // Generate unique Order Number
            $datePrefix = Carbon::now()->format('Ymd');
            $randomSuffix = strtoupper(Str::random(4));
            $orderNumber = 'DKT-'.$datePrefix.'-'.$randomSuffix;

            $totalOmset = 0;
            $totalModal = 0;
            $orderItemsData = [];

            foreach ($rawCart as $cartItem) {
                $productId = $cartItem['id'] ?? null;
                $qty = (int) ($cartItem['quantity'] ?? 1);

                if (! $productId || $qty <= 0) {
                    continue;
                }

                $product = Product::find($productId);
                if (! $product) {
                    continue;
                }

                $subtotal = $product->harga_jual * $qty;
                $subtotalModal = $product->modal * $qty;
                $subtotalProfit = $subtotal - $subtotalModal;

                $totalOmset += $subtotal;
                $totalModal += $subtotalModal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'price' => $product->harga_jual,
                    'modal' => $product->modal,
                    'subtotal' => $subtotal,
                    'subtotal_modal' => $subtotalModal,
                    'subtotal_profit' => $subtotalProfit,
                ];
            }

            if (empty($orderItemsData)) {
                DB::rollBack();

                return back()->withErrors(['cart_items' => 'Produk di keranjang tidak valid atau sudah tidak tersedia.'])->withInput();
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $request->input('customer_name'),
                'customer_phone' => $request->input('customer_phone'),
                'pickup_date' => $request->input('pickup_date'),
                'pickup_time' => $request->input('pickup_time') ?: '12:00',
                'pickup_location' => $pickupLocation,
                'notes' => $request->input('notes'),
                'payment_method' => 'QRIS',
                'payment_proof' => $proofPath,
                'total_omset' => $totalOmset,
                'total_modal' => $totalModal,
                'net_profit' => $totalOmset - $totalModal,
                'status' => 'menunggu_verifikasi',
            ]);

            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            DB::commit();

            return redirect()->route('order.success', $order->order_number)
                ->with('success', 'Pesanan Anda berhasil dikirim! Tim Dapur KaTanya akan segera memverifikasi pembayaran Anda.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Terjadi kesalahan saat memproses pesanan: '.$e->getMessage()])->withInput();
        }
    }

    /**
     * Show order confirmation & status details.
     */
    public function showOrder(string $orderNumber): View
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        $pickupSchedules = $this->activePickupSchedules();

        return view('customer.order-detail', compact('order', 'pickupSchedules'));
    }

    /**
     * Search and track order by phone number or order number.
     */
    public function trackOrder(Request $request): View
    {
        $query = $request->query('q');
        $orders = collect();

        if ($query) {
            $orders = Order::with('items')
                ->where('order_number', 'ilike', "%{$query}%")
                ->orWhere('customer_phone', 'like', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $pickupSchedules = $this->activePickupSchedules();

        return view('customer.track-order', compact('orders', 'query', 'pickupSchedules'));
    }

    /**
     * @return Collection<int, PickupSchedule>
     */
    private function activePickupSchedules(): Collection
    {
        return PickupSchedule::query()
            ->where('is_active', true)
            ->where('pickup_date', '>=', now('Asia/Jakarta')->toDateString())
            ->orderBy('pickup_date')
            ->get();
    }
}
