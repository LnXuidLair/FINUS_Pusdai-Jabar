<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Bukti transaksi memakai disk yang sama dengan bukti presensi.
 * Nama folder setiap jenis bukti tetap berbeda; nilai database adalah path relatif.
 * File lama pada disk public tetap bisa dibaca di localhost sebagai fallback.
 */
class TransactionProofStorage
{
    public function disk(): string
    {
        return (string) config('filesystems.attendance_disk', 'public');
    }

    public function store(UploadedFile $file, string $folder): string
    {
        if (! in_array($folder, [
            'bukti_pemasukan',
            'bukti_pengeluaran',
            'dokumen_pendukung_honorarium',
            'bukti-pembayaran-ziswaf',
        ], true)) {
            throw new \InvalidArgumentException('Folder bukti transaksi tidak dikenali.');
        }

        return $file->store($folder, $this->disk());
    }

    public function delete(?string $path): void
    {
        $disk = $this->existingDisk($path);
        if ($disk) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function response(?string $path)
    {
        $disk = $this->existingDisk($path);
        abort_unless($disk, 404, 'Bukti transaksi tidak ditemukan.');

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };

        return Storage::disk($disk)->response(
            $path,
            basename($path),
            [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'private, no-store',
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }

    private function existingDisk(?string $path): ?string
    {
        if (! is_string($path) || $path === '' || str_contains($path, '..')
            || str_starts_with($path, '/') || str_contains($path, "\\")) {
            return null;
        }

        // Cek disk utama lebih dulu (R2 di Vercel, public di localhost).
        // Fallback public hanya untuk bukti transaksi lama yang masih ada di lokal.
        foreach (array_unique([$this->disk(), 'public']) as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return $disk;
            }
        }

        return null;
    }
}