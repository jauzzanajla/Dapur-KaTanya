<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
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
            'images.*' => 'image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ], [
            'name.unique' => 'Nama produk ini sudah digunakan.',
            'modal.required' => 'Modal (HPP) wajib diisi untuk kalkulasi laba admin.',
            'harga_jual.required' => 'Harga jual ke pembeli wajib diisi.',
            'images.required' => 'Wajib mengunggah minimal 1 gambar produk.',
            'images.*.image' => 'File yang diunggah harus berupa gambar (jpg, png, webp, svg).',
        ]);

        $slug = Str::slug($request->input('name'));
        if (Product::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::random(4);
        }

        $product = Product::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'category' => $request->input('category'),
            'badge' => $request->input('badge'),
            'modal' => $request->input('modal'),
            'harga_jual' => $request->input('harga_jual'),
            'description' => $request->input('description'),
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($product->is_featured) {
            Product::where('id', '!=', $product->id)->where('is_featured', true)->update(['is_featured' => false]);
        }

        if ($request->hasFile('images')) {
            $uploadedFiles = $request->file('images');
            foreach ($uploadedFiles as $index => $file) {
                $filename = 'prod_'.$product->id.'_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
                try {
                    $path = $file->storeAs('products', $filename, 'public');
                } catch (\Throwable $exception) {
                    Log::warning('Product image upload failed.', [
                        'product_id' => $product->id,
                        'exception' => $exception->getMessage(),
                    ]);

                    return back()->withInput()->with('error', 'Gambar produk gagal diunggah ke storage. Periksa konfigurasi storage lalu coba kembali.');
                }

                if (! $path) {
                    return back()->withInput()->with('error', 'Gambar produk gagal diunggah ke storage. Periksa konfigurasi storage lalu coba kembali.');
                }

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0, // First image is primary by default
                    'sort_order' => $index + 1,
                ]);
            }
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
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $product->update([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'badge' => $request->input('badge'),
            'modal' => $request->input('modal'),
            'harga_jual' => $request->input('harga_jual'),
            'description' => $request->input('description'),
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($product->is_featured) {
            Product::where('id', '!=', $product->id)->where('is_featured', true)->update(['is_featured' => false]);
        }

        // Upload additional images if any
        if ($request->hasFile('new_images')) {
            $lastSort = $product->images()->max('sort_order') ?? 0;
            $hasPrimary = $product->images()->where('is_primary', true)->exists();

            foreach ($request->file('new_images') as $index => $file) {
                $filename = 'prod_'.$product->id.'_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
                try {
                    $path = $file->storeAs('products', $filename, 'public');
                } catch (\Throwable $exception) {
                    Log::warning('Additional product image upload failed.', [
                        'product_id' => $product->id,
                        'exception' => $exception->getMessage(),
                    ]);

                    return back()->withInput()->with('error', 'Gambar tambahan gagal diunggah ke storage. Periksa konfigurasi storage lalu coba kembali.');
                }

                if (! $path) {
                    return back()->withInput()->with('error', 'Gambar tambahan gagal diunggah ke storage. Periksa konfigurasi storage lalu coba kembali.');
                }

                $lastSort++;
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => ! $hasPrimary && $index === 0,
                    'sort_order' => $lastSort,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Produk '{$product->name}' berhasil diperbarui.");
    }

    public function deleteImage(ProductImage $image)
    {
        $productId = $image->product_id;
        $wasPrimary = $image->is_primary;

        // Delete file from disk if stored locally
        if ($image->image_path) {
            try {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            } catch (\Throwable $exception) {
                Log::warning('Product image file could not be deleted.', [
                    'path' => $image->image_path,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        $image->delete();

        // If primary was deleted, assign next image as primary
        if ($wasPrimary) {
            $next = ProductImage::where('product_id', $productId)->first();
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
        // Delete images
        foreach ($product->images as $img) {
            if ($img->image_path) {
                try {
                    if (Storage::disk('public')->exists($img->image_path)) {
                        Storage::disk('public')->delete($img->image_path);
                    }
                } catch (\Throwable $exception) {
                    Log::warning('Product image file could not be deleted during product removal.', [
                        'path' => $img->image_path,
                        'exception' => $exception->getMessage(),
                    ]);
                }
            }
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
}
