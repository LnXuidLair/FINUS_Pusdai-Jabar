<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Aws\S3\S3Client;

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

    public const MAX_UPLOAD_BYTES = 5 * 1024 * 1024;

    /** Generate a short-lived, path-specific R2 PUT. Credentials never reach the browser. */
    public function prepareDirectUpload(Request $request, string $folder, string $filename, string $mime, int $size): array
    {
        $this->assertAllowedFolder($folder);
        if (config('filesystems.disks.'.$this->disk().'.driver') !== 's3') {
            throw ValidationException::withMessages(['bukti_pembayaran' => 'Upload langsung hanya tersedia saat penyimpanan R2 aktif.']);
        }
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $types = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'pdf' => 'application/pdf'];
        if (!isset($types[$ext]) || $types[$ext] !== $mime || $size < 1 || $size > self::MAX_UPLOAD_BYTES) {
            throw ValidationException::withMessages(['bukti_pembayaran' => 'Berkas harus JPG, PNG atau PDF, maksimal 5 MB.']);
        }
        $key = $folder.'/'.date('Y/m').'/'.\Illuminate\Support\Str::uuid()->toString().'.'.$ext;
        $client = $this->r2Client();
        $cfg = config('filesystems.disks.'.$this->disk());
        $command = $client->getCommand('PutObject', [
            'Bucket' => $cfg['bucket'], 'Key' => $key, 'ContentType' => $mime,
        ]);
        $uri = $client->createPresignedRequest($command, '+10 minutes')->getUri();
        $identity = $this->identity($request);
        $token = Crypt::encryptString(json_encode([
            'key' => $key, 'folder' => $folder, 'mime' => $mime,
            'guard' => $identity[0], 'uid' => $identity[1], 'created' => time(),
        ], JSON_THROW_ON_ERROR));
        return ['url' => (string)$uri, 'token' => $token, 'content_type' => $mime];
    }

    /** Accept ordinary localhost file uploads OR a verified upload already sent to private R2. */
    public function storeFromRequest(Request $request, string $field, string $folder): ?string
    {
        $this->assertAllowedFolder($folder);
        if ($request->hasFile($field)) {
            return $this->store($request->file($field), $folder);
        }
        $token = $request->input($field.'_upload_token');
        if (!$token) return null;
        try {
            $data = json_decode(Crypt::decryptString((string)$token), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([$field => 'Token upload tidak valid. Pilih kembali berkas.']);
        }
        $identity = $this->identity($request);
        $path = $data['key'] ?? null;
        if (!is_string($path) || !str_starts_with($path, $folder.'/') ||
            ($data['folder'] ?? null) !== $folder ||
            ($data['guard'] ?? null) !== $identity[0] || (int)($data['uid'] ?? 0) !== $identity[1] ||
            abs(time() - (int)($data['created'] ?? 0)) > 900 ||
            !preg_match('~\.(jpg|jpeg|png|pdf)$~i', $path)) {
            throw ValidationException::withMessages([$field => 'Upload kedaluwarsa atau bukan milik akun ini. Silakan pilih ulang.']);
        }
        $cfg = config('filesystems.disks.'.$this->disk());
        if (($cfg['driver'] ?? null) !== 's3') {
            throw ValidationException::withMessages([$field => 'Disk R2 tidak aktif.']);
        }
        try {
            $client = $this->r2Client();
            $head = $client->headObject(['Bucket' => $cfg['bucket'], 'Key' => $path]);
            $size = (int)($head['ContentLength'] ?? 0);
            $mime = strtolower((string)($head['ContentType'] ?? ''));
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $expectedMime = in_array($ext, ['jpg', 'jpeg']) ? 'image/jpeg' : ($ext === 'png' ? 'image/png' : 'application/pdf');
            if ($size < 1 || $size > self::MAX_UPLOAD_BYTES || $mime !== $expectedMime || $mime !== ($data['mime'] ?? '')) {
                throw new \RuntimeException('Ukuran atau tipe dokumen tidak cocok.');
            }
            // Periksa magic bytes, bukan hanya nama ekstensi atau Content-Type dari browser.
            $partial = $client->getObject(['Bucket' => $cfg['bucket'], 'Key' => $path, 'Range' => 'bytes=0-7']);
            $signature = (string)$partial['Body'];
            $valid = match ($ext) {
                'jpg', 'jpeg' => str_starts_with($signature, "\xFF\xD8\xFF"),
                'png' => str_starts_with($signature, "\x89PNG\r\n\x1A\n"),
                'pdf' => str_starts_with($signature, '%PDF-'),
                default => false,
            };
            if (!$valid) throw new \RuntimeException('Isi file tidak sesuai dengan format dokumen.');
        } catch (\Throwable $e) {
            report($e);
            throw ValidationException::withMessages([$field => 'File bukti tidak ditemukan atau tidak valid di R2. Silakan unggah ulang.']);
        }
        return $path;
    }

    private function identity(Request $request): array
    {
        foreach (['admin', 'pegawai', 'jamaah'] as $guard) {
            if ($user = $request->user($guard)) return [$guard, (int)$user->getAuthIdentifier()];
        }
        abort(403);
    }

    private function assertAllowedFolder(string $folder): void
    {
        if (!in_array($folder, ['bukti_pemasukan', 'bukti_pengeluaran', 'dokumen_pendukung_honorarium'], true)) {
            throw new \InvalidArgumentException('Folder dokumen tidak diizinkan.');
        }
    }

    private function r2Client(): S3Client
    {
        $cfg = config('filesystems.disks.'.$this->disk());
        return new S3Client([
            'version' => 'latest', 'region' => $cfg['region'] ?? 'auto',
            'endpoint' => $cfg['endpoint'] ?? null,
            'use_path_style_endpoint' => (bool)($cfg['use_path_style_endpoint'] ?? false),
            'credentials' => ['key' => $cfg['key'], 'secret' => $cfg['secret']],
            // R2 presigned PUT must not require SDK-generated checksum headers.
            'request_checksum_calculation' => 'when_required',
        ]);
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

        // Di Vercel respons Function juga terbatas ~4,5 MB. Setelah route Laravel
        // memverifikasi akses, kirim URL R2 bertanda tangan singkat agar gambar/PDF 5 MB
        // tidak melintasi Function. Bucket tetap private.
        if (config('filesystems.disks.'.$disk.'.driver') === 's3') {
            return redirect()->away(Storage::disk($disk)->temporaryUrl($path, now()->addMinutes(3)));
        }

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