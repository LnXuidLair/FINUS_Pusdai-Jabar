<?php

namespace App\Observers;

use App\Models\ZiswafPenerimaan;
use App\Services\FinusNotificationService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class FinusZiswafObserver implements ShouldHandleEventsAfterCommit
{
    public function created(ZiswafPenerimaan $transaction): void
    {
        if (! $transaction->organization_id) {
            return;
        }
        $this->dispatchState($transaction);
    }

    public function updated(ZiswafPenerimaan $transaction): void
    {
        if (! $transaction->organization_id) {
            return;
        }
        if ($transaction->wasChanged(['payment_status', 'status_verifikasi'])) {
            $this->dispatchState($transaction);
        }
    }

    private function dispatchState(ZiswafPenerimaan $transaction): void
    {
        $state = (string) $transaction->payment_status;
        $verified = (string) $transaction->status_verifikasi;
        $kind = ucwords(str_replace(['_', '-'], ' ', (string) $transaction->jenis_ziswaf));
        $key = null;
        $title = null;
        $message = null;
        if ($verified === 'diterima' && in_array($state, ['settlement', 'capture', 'manual_paid'], true)) {
            $key = 'ziswaf:paid:'.$transaction->id;
            $title = $kind.' berhasil diterima';
            $message = 'Pembayaran '.$kind.' terverifikasi sebesar Rp'.number_format((int) $transaction->nominal, 0, ',', '.').'.';
        } elseif ($verified === 'pending' && in_array($state, ['manual_pending'], true)) {
            $key = 'ziswaf:manual:'.$transaction->id;
            $title = 'Verifikasi ZISWAF baru';
            $message = 'Ada pembayaran manual '.$kind.' yang menunggu verifikasi.';
        } elseif ($verified === 'ditolak' && in_array($state, ['deny', 'cancel', 'expire', 'failure', 'manual_rejected'], true)) {
            $key = 'ziswaf:failed:'.$transaction->id;
            $title = 'Pembayaran ZISWAF tidak berhasil';
            $message = 'Status pembayaran '.$kind.' berubah menjadi tidak berhasil.';
        }
        if ($key) {
            $isParking = in_array((string) $transaction->jenis_ziswaf, ['parkir', 'hasil_parkir'], true);
            $category = $isParking ? 'pemasukan' : 'ziswaf';
            $detailUrl = $isParking ? route('admin.pemasukan.index') : route('admin.ziswaf.transaksi.index');
            app(FinusNotificationService::class)->notifyAdmins((int) $transaction->organization_id,
                $key, $category, $title, $message, $detailUrl);
            // Jamaah tidak terikat organisasi tetap hanya melihat transaksi miliknya.
            if ($verified === 'diterima' && str_starts_with($key, 'ziswaf:paid:') && $transaction->muzakki_id) {
                $donor = \App\Models\User::query()
                    ->where('id', $transaction->muzakki_id)
                    ->where('role', \App\Models\User::ROLE_JAMAAH)->first();
                if ($donor) {
                    app(FinusNotificationService::class)->notify($donor, (int) $transaction->organization_id,
                        'ziswaf:receipt:'.$transaction->id, 'ziswaf',
                        'Pembayaran ZISWAF terverifikasi', 'Pembayaran Anda berhasil dikonfirmasi.',
                        route('jamaah.riwayat.index'));
                }
            }
        }
    }
}
