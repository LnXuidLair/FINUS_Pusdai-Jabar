<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Models\ZiswafPenerimaan;
use App\Services\TransactionProofStorage;
use Illuminate\Http\Request;

/** Streaming bukti menggunakan hak akses akun, bukan /storage URL publik. */
class TransactionProofController extends Controller
{
    public function pemasukan(Request $request, string $pemasukan, TransactionProofStorage $proof)
    {
        $item = ZiswafPenerimaan::query()->findOrFail($pemasukan);
        $this->assertInternalOrganization($request, (int) $item->organization_id);

        return $proof->response($item->bukti_pembayaran);
    }

    public function pengeluaran(Request $request, string $pengeluaran, string $jenis, TransactionProofStorage $proof)
    {
        abort_unless(in_array($jenis, ['pembayaran', 'surat-tugas', 'dokumen-pendukung'], true), 404);

        if (str_starts_with($pengeluaran, 'gaji_')) {
            abort_unless($jenis === 'pembayaran', 404);
            $id = substr($pengeluaran, 5);
            abort_unless($id !== '' && ctype_digit($id), 404);
            $gaji = Penggajian::query()->findOrFail($id);
            $this->assertInternalOrganization($request, (int) $gaji->organization_id);
            abort_unless($gaji->status_penggajian === 'sudah_dibayar', 404);

            return $proof->response($gaji->bukti_pembayaran);
        }

        abort_unless($pengeluaran !== '' && ctype_digit($pengeluaran), 404);
        $item = Pengeluaran::query()->findOrFail($pengeluaran);
        $this->assertInternalOrganization($request, (int) $item->organization_id);

        return $proof->response(in_array($jenis, ['surat-tugas', 'dokumen-pendukung'], true)
            ? $item->bukti_surat_tugas
            : $item->bukti_pembayaran);
    }

    public function suratTugasSaya(Request $request, string $pengeluaran, TransactionProofStorage $proof)
    {
        $pegawai = $request->user('pegawai')?->pegawai;
        abort_unless($pegawai, 404);
        $item = Pengeluaran::query()->where('id_pegawai', $pegawai->id)
            ->where('status_verifikasi', 'diterima')
            ->whereHas('coaDebit', fn ($query) => $query->where('kode_akun', '5104'))
            ->findOrFail($pengeluaran);
        abort_unless((int) $pegawai->organization_id === (int) $item->organization_id, 404);

        return $proof->response($item->bukti_surat_tugas);
    }

    public function jamaah(Request $request, string $transaksi, TransactionProofStorage $proof)
    {
        $item = ZiswafPenerimaan::query()->findOrFail($transaksi);
        abort_unless((int) $item->muzakki_id === (int) $request->user('jamaah')?->id, 404);

        return $proof->response($item->bukti_pembayaran);
    }

    private function assertInternalOrganization(Request $request, int $organizationId): void
    {
        $guard = $request->routeIs('admin.*') ? 'admin' : 'pegawai';
        $user = $request->user($guard);
        abort_unless($user && $organizationId > 0
            && (int) $user->organization_id === $organizationId, 404);
    }
}
