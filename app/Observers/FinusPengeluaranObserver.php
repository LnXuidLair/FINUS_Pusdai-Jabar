<?php

namespace App\Observers;

use App\Models\Pengeluaran;
use App\Services\FinusNotificationService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class FinusPengeluaranObserver implements ShouldHandleEventsAfterCommit
{
    public function created(Pengeluaran $pengeluaran): void
    {
        if (! $pengeluaran->organization_id) return;
        app(FinusNotificationService::class)->notifyAdmins((int) $pengeluaran->organization_id,
            'pengeluaran:created:'.$pengeluaran->id, 'pengeluaran', 'Pengeluaran baru dicatat',
            'Ada pengeluaran yang baru dicatat di FINUS.', route('admin.pengeluaran.index'));
    }
}
