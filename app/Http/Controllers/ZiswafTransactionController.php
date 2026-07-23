<?php

namespace App\Http\Controllers;

use App\Models\ZiswafPenerimaan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ZiswafTransactionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'jenis' => ['nullable', 'string', 'max:50'],
            'payment_status' => ['nullable', 'string', 'max:50'],
            'status_verifikasi' => ['nullable', 'string', 'max:50'],
        ]);

        $query = ZiswafPenerimaan::with('muzakki')
            ->latest('tanggal')
            ->latest('id');

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);

            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('order_id', 'like', '%' . $search . '%')
                    ->orWhere('keterangan', 'like', '%' . $search . '%')
                    ->orWhereHas('muzakki', function (Builder $userQuery) use ($search): void {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        if (!empty($filters['jenis'])) {
            $query->where('jenis_ziswaf', $filters['jenis']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['status_verifikasi'])) {
            $query->where('status_verifikasi', $filters['status_verifikasi']);
        }

        $summaryBase = ZiswafPenerimaan::query();

        $summary = [
            'total' => (int) (clone $summaryBase)->sum('nominal'),
            'diterima' => (int) (clone $summaryBase)
                ->where('status_verifikasi', 'diterima')
                ->sum('nominal'),
            'pending' => (int) (clone $summaryBase)
                ->where(function (Builder $builder): void {
                    $builder->where('status_verifikasi', 'pending')
                        ->orWhereNull('status_verifikasi');
                })
                ->sum('nominal'),
            'ditolak' => (int) (clone $summaryBase)
                ->where('status_verifikasi', 'ditolak')
                ->sum('nominal'),
        ];

        $transaksi = $query->paginate(10)->withQueryString();

        return view('admin.ziswaf-transaksi.index', [
            'transaksi' => $transaksi,
            'filters' => $filters,
            'summary' => $summary,
            'jenisLabels' => $this->jenisLabels(),
            'paymentStatusLabels' => $this->paymentStatusLabels(),
            'statusVerifikasiLabels' => $this->statusVerifikasiLabels(),
            'metodeLabels' => $this->metodeLabels(),
        ]);
    }

    public function terima(Request $request, ZiswafPenerimaan $transaksi)
    {
        $transaksi->update([
            'payment_status' => $transaksi->payment_status === 'manual_pending'
                ? 'manual_paid'
                : $transaksi->payment_status,
            'status_verifikasi' => 'diterima',
            'catatan_verifikasi' => $request->input(
                'catatan_verifikasi',
                'Transaksi diverifikasi manual oleh admin.'
            ),
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'paid_at' => $transaksi->paid_at ?? now(),
        ]);

        return back()->with('success', 'Transaksi berhasil diterima.');
    }

    public function tolak(Request $request, ZiswafPenerimaan $transaksi)
    {
        $validated = $request->validate([
            'catatan_verifikasi' => ['required', 'string', 'max:1000'],
        ]);

        $transaksi->update([
            'status_verifikasi' => 'ditolak',
            'catatan_verifikasi' => $validated['catatan_verifikasi'],
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Transaksi berhasil ditolak.');
    }

    private function jenisLabels(): array
    {
        return [
            'zakat_maal' => 'Zakat Maal',
            'zakat_fitrah' => 'Zakat Fitrah',
            'zakat_penghasilan' => 'Zakat Penghasilan',
            'infaq' => 'Infak',
            'shadaqah' => 'Sedekah',
            'wakaf' => 'Wakaf',
            'fidyah' => 'Fidyah',
        ];
    }

    private function paymentStatusLabels(): array
    {
        return [
            'manual_pending' => 'Manual - Menunggu Verifikasi',
            'manual_paid' => 'Manual - Dibayar',
            'pending' => 'Gateway - Menunggu Pembayaran',
            'settlement' => 'Gateway - Berhasil',
            'capture' => 'Gateway - Berhasil',
            'deny' => 'Gateway - Ditolak',
            'cancel' => 'Gateway - Dibatalkan',
            'expire' => 'Gateway - Kedaluwarsa',
            'failure' => 'Gateway - Gagal',
        ];
    }

    private function statusVerifikasiLabels(): array
    {
        return [
            'pending' => 'Menunggu',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
        ];
    }

    private function metodeLabels(): array
    {
        return [
            'manual_transfer' => 'Transfer Bank Manual',
            'qris_manual' => 'QRIS Manual',
            'qris' => 'QRIS',
            'other_qris' => 'QRIS',
            'virtual_account' => 'Virtual Account',
            'e_money' => 'E-Money / E-Wallet',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
        ];
    }
}