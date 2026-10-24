<?php

namespace Tests\Feature;

use App\Services\TransactionProofStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TransactionProofStorageTest extends TestCase
{
    public function test_income_and_expense_proofs_use_separate_folders_on_attendance_disk(): void
    {
        config()->set('filesystems.attendance_disk', 's3');
        Storage::fake('s3');

        $proof = app(TransactionProofStorage::class);
        $pemasukan = $proof->store(UploadedFile::fake()->create('pemasukan.pdf', 10, 'application/pdf'), 'bukti_pemasukan');
        $pengeluaran = $proof->store(UploadedFile::fake()->create('pengeluaran.pdf', 10, 'application/pdf'), 'bukti_pengeluaran');

        $this->assertStringStartsWith('bukti_pemasukan/', $pemasukan);
        $this->assertStringStartsWith('bukti_pengeluaran/', $pengeluaran);
        Storage::disk('s3')->assertExists($pemasukan);
        Storage::disk('s3')->assertExists($pengeluaran);
        $this->assertSame('application/pdf', $proof->response($pemasukan)->headers->get('content-type'));
        $this->assertSame('application/pdf', $proof->response($pengeluaran)->headers->get('content-type'));
    }

    public function test_legacy_expense_folder_remains_readable_without_moving_database_paths(): void
    {
        config()->set('filesystems.attendance_disk', 's3');
        Storage::fake('s3');
        Storage::fake('public');
        Storage::disk('public')->put('bukti_pembayaran/arsip-lama.pdf', '%PDF-1.4 contoh');

        $response = app(TransactionProofStorage::class)->response('bukti_pembayaran/arsip-lama.pdf');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        Storage::disk('public')->assertExists('bukti_pembayaran/arsip-lama.pdf');
    }

    public function test_deletion_removes_only_the_matching_file(): void
    {
        config()->set('filesystems.attendance_disk', 's3');
        Storage::fake('s3');
        Storage::disk('s3')->put('bukti_pemasukan/masuk.pdf', 'bukti masuk');
        Storage::disk('s3')->put('bukti_pengeluaran/keluar.pdf', 'bukti keluar');

        app(TransactionProofStorage::class)->delete('bukti_pengeluaran/keluar.pdf');

        Storage::disk('s3')->assertMissing('bukti_pengeluaran/keluar.pdf');
        Storage::disk('s3')->assertExists('bukti_pemasukan/masuk.pdf');
    }
}
