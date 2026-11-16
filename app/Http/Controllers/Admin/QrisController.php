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
    private const DISK = 'public';

    private const EXTENSIONS = ['png', 'jpg', 'jpeg'];

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
            'qris_image.uploaded' => 'Gambar gagal terkirim ke server. Kemungkinan ukurannya melebihi batas upload server.',
            'qris_image.image' => 'File harus berupa gambar.',
            'qris_image.mimes' => 'Gambar QRIS harus berformat PNG atau JPG.',
            'qris_image.max' => 'Ukuran gambar QRIS maksimal 5MB.',
        ]);

        $file = $request->file('qris_image');
        $filename = 'qris-pembayaran.'.$file->extension();

        try {
            $path = $file->storeAs('', $filename, self::DISK);

            if (! $path) {
                throw new \RuntimeException("storeAs() tidak mengembalikan path untuk {$filename}");
            }
        } catch (\Throwable $exception) {
            $reason = $this->describe($exception);

            Log::error('QRIS upload failed.', [
                'filename' => $filename,
                'exception' => $reason,
            ]);

            $message = 'Gambar QRIS gagal diunggah ke storage. Periksa konfigurasi storage lalu coba kembali.';
            if (config('app.debug')) {
                $message .= ' ('.$reason.')';
            }

            return back()->with('error', $message);
        }

        // Hapus file QRIS lama dengan ekstensi lain (delete langsung, tanpa exists()).
        foreach (self::EXTENSIONS as $extension) {
            $oldPath = "qris-pembayaran.{$extension}";
            if ($oldPath === $path) {
                continue;
            }

            try {
                Storage::disk(self::DISK)->delete($oldPath);
            } catch (\Throwable $exception) {
                Log::warning('Old QRIS file could not be deleted.', [
                    'path' => $oldPath,
                    'exception' => $this->describe($exception),
                ]);
            }
        }

        return redirect()->route('admin.qris.index')->with('success', 'Gambar QRIS berhasil diperbarui.');
    }

    private function currentQrisPath(): ?string
    {
        $disk = config('filesystems.disks.'.self::DISK, []);
        if (($disk['driver'] ?? null) === 's3' && (empty($disk['key']) || empty($disk['secret']))) {
            Log::warning('QRIS lookup skipped because S3 credentials are not configured.');

            return null;
        }

        foreach (self::EXTENSIONS as $extension) {
            $path = "qris-pembayaran.{$extension}";

            try {
                if (Storage::disk(self::DISK)->fileExists($path)) {
                    return $path;
                }
            } catch (\Throwable $exception) {
                Log::warning('QRIS storage lookup failed in the admin page.', [
                    'path' => $path,
                    'exception' => $this->describe($exception),
                ]);

                return null;
            }
        }

        return null;
    }

    /**
     * Pesan error lengkap, termasuk alasan asli dari S3 (mis. InvalidAccessKeyId)
     * yang biasanya tersembunyi di exception sebelumnya.
     */
    private function describe(\Throwable $exception): string
    {
        $message = $exception->getMessage();
        $previous = $exception->getPrevious();

        if ($previous && method_exists($previous, 'getAwsErrorCode') && $previous->getAwsErrorCode()) {
            $message .= ' — '.$previous->getAwsErrorCode().': '.$previous->getAwsErrorMessage();
        } elseif ($previous) {
            $message .= ' — '.$previous->getMessage();
        }

        return $message;
    }
}