<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ProductController extends Controller
{
    /**
     * Disk penyimpanan gambar produk. Atur lewat MEDIA_DISK di .env
     * (lokal: public, production: s3 / r2 / supabase).
     */
    private function mediaDisk(): string
    {
        return config('filesystems.media_disk', 'public');
    }

    private function uploadErrorMessage(\Throwable $e): string
    {
        $message = 'Gambar produk gagal diunggah ke storage. Periksa konfigurasi storage lalu coba kembali.';

        return config('app.debug') ? $message.' ('.$e->getMessage().')' : $message;
    }

    /**
     * Simpan satu file ke disk media. Melempar exception bila gagal.
     */
    private function storeImageFile(UploadedFile $file, Product $product): string
    {
        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension();
        $filename = 'prod_'.$product->id.'_'.time().'_'.Str::random(6).'.'.$extension;

        // Tanpa 'visibility' => 'public': Supabase Storage tidak mendukung ACL S3.
        // Akses publik diatur dari setelan bucket (Public bucket) di dashboard Supabase.
        $path = $file->storeAs('products', $filename, $this->mediaDisk());

        if (! $path) {
            throw new RuntimeException("storeAs() tidak mengembalikan path untuk {$filename}");
        }

        return $path;
    }

    private function deleteImageFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        try {
            // Langsung delete tanpa exists(): pengecekan exists() gagal di Supabase Storage,
            // dan delete file yang tidak ada di S3 tidak menimbulkan error.
            Storage::disk($this->mediaDisk())->delete($path);
        } catch (\Throwable $exception) {
            Log::warning('Product image file could not be deleted.', [
                'path' => $path,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');

        $query = Product::with('images');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if ($category && in_array($category, ['makanan', 'minuman', 'paket'])) {
            $query->where('category', $category);
        }

        $products = $query->orderBy('id', 'desc')->paginate(15);

        return view('admin.products.index', compact('products', 'search', 'category'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150|unique:products,name',
            'category' => 'required|in:makanan,minuman,paket',
            'badge' => 'nullable|string|max:50',
            'modal' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'description' => 'required|string',
            'images' => 'required|array|min:1',
            'images.*' => 'file|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'is_active' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ], [
            'name.unique' => 'Nama produk ini sudah digunakan.',
            'modal.required' => 'Modal (HPP) wajib diisi untuk kalkulasi laba admin.',
            'harga_jual.required' => 'Harga jual ke pembeli wajib diisi.',
            'images.required' => 'Wajib mengunggah minimal 1 gambar produk.',
            'images.*.uploaded' => 'Gambar gagal terkirim ke server. Kemungkinan ukurannya melebihi batas upload server.',
            'images.*.mimes' => 'File yang diunggah harus berupa gambar (jpg, png, webp, svg).',
            'images.*.max' => 'Ukuran tiap gambar maksimal 4 MB.',
        ]);

        $slug = Str::slug($request->input('name'));
        if (Product::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::random(4);
        }

        $storedPaths = [];

        try {
            $product = DB::transaction(function () use ($request, $slug, &$storedPaths) {
                $product = Product::create([
                    'name' => $request->input('name'),
                    'slug' => $slug,
                    'category' => $request->input('category'),
                    'badge' => $request->input('badge'),
                    'modal' => $request->input('modal'),
                    'harga_jual' => $request->input('harga_jual'),
                    'description' => $request->input('description'),
                    'is_active' => $request->boolean('is_active', true),
                    'is_available' => $request->boolean('is_available', true),
                    'is_featured' => $request->boolean('is_featured'),
                ]);

                foreach ($request->file('images', []) as $index => $file) {
                    $path = $this->storeImageFile($file, $product);
                    $storedPaths[] = $path;

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0,
                        'sort_order' => $index + 1,
                    ]);
                }

                if ($product->is_featured) {
                    Product::where('id', '!=', $product->id)->where('is_featured', true)->update(['is_featured' => false]);
                }

                return $product;
            });
        } catch (\Throwable $exception) {
            // Transaksi DB sudah di-rollback; bersihkan file yang sempat terunggah.
            foreach ($storedPaths as $path) {
                $this->deleteImageFile($path);
            }

            Log::error('Product image upload failed.', [
                'disk' => $this->mediaDisk(),
                'exception' => $exception->getMessage(),
            ]);

            return back()->withInput()->with('error', $this->uploadErrorMessage($exception));
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$product->name}' berhasil ditambahkan beserta gambar katalog!");
    }

    public function edit(Product $product)
    {
        $product->load('images');

        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:150|unique:products,name,'.$product->id,
            'category' => 'required|in:makanan,minuman,paket',
            'badge' => 'nullable|string|max:50',
            'modal' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'description' => 'required|string',
            'new_images' => 'nullable|array',
            'new_images.*' => 'file|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'is_active' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ], [
            'new_images.*.uploaded' => 'Gambar gagal terkirim ke server. Kemungkinan ukurannya melebihi batas upload server.',
            'new_images.*.mimes' => 'File yang diunggah harus berupa gambar (jpg, png, webp, svg).',
            'new_images.*.max' => 'Ukuran tiap gambar maksimal 4 MB.',
        ]);

        $storedPaths = [];

        try {
            DB::transaction(function () use ($request, $product, &$storedPaths) {
                $product->update([
                    'name' => $request->input('name'),
                    'category' => $request->input('category'),
                    'badge' => $request->input('badge'),
                    'modal' => $request->input('modal'),
                    'harga_jual' => $request->input('harga_jual'),
                    'description' => $request->input('description'),
                    'is_active' => $request->boolean('is_active'),
                    'is_available' => $request->boolean('is_available', true),
                    'is_featured' => $request->boolean('is_featured'),
                ]);

                if ($product->is_featured) {
                    Product::where('id', '!=', $product->id)->where('is_featured', true)->update(['is_featured' => false]);
                }

                if ($request->hasFile('new_images')) {
                    $lastSort = $product->images()->max('sort_order') ?? 0;
                    $hasPrimary = $product->images()->where('is_primary', true)->exists();

                    foreach ($request->file('new_images') as $index => $file) {
                        $path = $this->storeImageFile($file, $product);
                        $storedPaths[] = $path;

                        $lastSort++;
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                            'is_primary' => ! $hasPrimary && $index === 0,
                            'sort_order' => $lastSort,
                        ]);
                    }
                }
            });
        } catch (\Throwable $exception) {
            foreach ($storedPaths as $path) {
                $this->deleteImageFile($path);
            }

            Log::error('Additional product image upload failed.', [
                'product_id' => $product->id,
                'disk' => $this->mediaDisk(),
                'exception' => $exception->getMessage(),
            ]);

            return back()->withInput()->with('error', $this->uploadErrorMessage($exception));
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$product->name}' berhasil diperbarui.");
    }

    public function deleteImage(ProductImage $image)
    {
        $productId = $image->product_id;
        $wasPrimary = $image->is_primary;

        $this->deleteImageFile($image->image_path);
        $image->delete();

        if ($wasPrimary) {
            $next = ProductImage::where('product_id', $productId)->orderBy('sort_order')->first();
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Gambar berhasil dihapus dari galeri produk.');
    }

    public function setPrimaryImage(ProductImage $image)
    {
        ProductImage::where('product_id', $image->product_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Gambar utama berhasil diubah.');
    }

    public function destroy(Product $product)
    {
        $name = $product->name;

        foreach ($product->images as $img) {
            $this->deleteImageFile($img->image_path);
            $img->delete();
        }
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$name}' telah dihapus.");
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);
        $statusStr = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status produk '{$product->name}' berhasil {$statusStr}.");
    }

    public function toggleAvailability(Product $product): RedirectResponse
    {
        $product->update(['is_available' => ! $product->is_available]);
        $availability = $product->is_available ? 'tersedia' : 'habis';

        return back()->with('success', "Ketersediaan menu '{$product->name}' diubah menjadi {$availability}.");
    }
}
