<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QrisController extends Controller
{
    public function index(): View
    {
        $qrisPath = $this->currentQrisPath();

        return view('admin.qris.index', compact('qrisPath'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'qris_image' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
        ], [
            'qris_image.required' => 'Pilih gambar QRIS yang ingin diunggah.',
            'qris_image.image' => 'File harus berupa gambar.',
            'qris_image.mimes' => 'Gambar QRIS harus berformat PNG atau JPG.',
            'qris_image.max' => 'Ukuran gambar QRIS maksimal 5MB.',
        ]);

        $file = $request->file('qris_image');
        $filename = 'qris-pembayaran.'.$file->extension();
        try {
            $path = $file->storeAs('', $filename, 'public');
        } catch (\Throwable $exception) {
            Log::warning('QRIS upload failed.', ['exception' => $exception->getMessage()]);

            return back()->with('error', 'Gambar QRIS gagal diunggah ke storage. Periksa konfigurasi storage lalu coba kembali.');
        }

        if (! $path) {
            return back()->with('error', 'Gambar QRIS gagal disimpan. Silakan coba kembali.');
        }

        foreach (['png', 'jpg', 'jpeg'] as $extension) {
            $oldPath = "qris-pembayaran.{$extension}";
            if ($oldPath !== $path) {
                try {
                    Storage::disk('public')->delete($oldPath);
                } catch (\Throwable $exception) {
                    Log::warning('Old QRIS file could not be deleted.', [
                        'path' => $oldPath,
                        'exception' => $exception->getMessage(),
                    ]);
                }
            }
        }

        return redirect()->route('admin.qris.index')->with('success', 'Gambar QRIS berhasil diperbarui.');
    }

    private function currentQrisPath(): ?string
    {
        foreach (['png', 'jpg', 'jpeg'] as $extension) {
            $path = "qris-pembayaran.{$extension}";
            try {
                if (Storage::disk('public')->exists($path)) {
                    return $path;
                }
            } catch (\Throwable $exception) {
                Log::warning('QRIS storage lookup failed in the admin page.', [
                    'path' => $path,
                    'exception' => $exception->getMessage(),
                ]);

                return null;
            }
        }

        return null;
    }
}
