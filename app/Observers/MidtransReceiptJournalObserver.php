<?php

namespace App\Observers;

use App\Models\ZiswafPenerimaan;
use App\Services\Accounting\Psak109PostingService;
use Illuminate\Support\Facades\DB;

class MidtransReceiptJournalObserver
{
    public function saved(ZiswafPenerimaan $receipt): void
    {
        if ($receipt->payment_gateway !== 'midtrans'
            || $receipt->status_verifikasi !== 'diterima'
            || ! in_array($receipt->payment_status, ['settlement', 'capture'], true)
            || ($receipt->payment_status === 'capture'
                && ! in_array($receipt->fraud_status, ['accept', null, ''], true))) {
            return;
        }

        DB::transaction(function () use ($receipt): void {
            // Serialize webhook and polling confirmations to avoid duplicate journals.
            $locked = ZiswafPenerimaan::query()->lockForUpdate()->findOrFail($receipt->id);
            if (! $locked->jurnal_id) {
                app(Psak109PostingService::class)->postPenerimaan($locked);
            }
            $receipt->jurnal_id = $locked->jurnal_id;
        });
    }
}
