<?php

namespace App\Observers;

use App\Models\Pegawai;
use App\Models\Presensi;
use App\Models\User;
use App\Services\FinusNotificationService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class FinusPresensiObserver implements ShouldHandleEventsAfterCommit
{
    public function created(Presensi $presensi): void
    {
        if (! $presensi->organization_id) {
            return;
        }
        $this->service()->notifyAdmins((int) $presensi->organization_id, 'presensi:created:'.$presensi->id,
            'presensi', 'Presensi pegawai baru', 'Ada catatan presensi pegawai yang baru masuk.', route('admin.presensi.index'));
    }

    public function updated(Presensi $presensi): void
    {
        if (! $presensi->organization_id) {
            return;
        }
        if ($presensi->wasChanged('jam_pulang') && filled($presensi->jam_pulang) && $presensi->input_pulang_role === 'pegawai') {
            $this->service()->notifyAdmins((int) $presensi->organization_id, 'presensi:pulang:'.$presensi->id,
                'presensi', 'Presensi pulang pegawai', 'Pegawai telah mencatat waktu pulang.', route('admin.presensi.index'));
        }
        if ($presensi->wasChanged('is_approved') && $presensi->is_approved) {
            $pegawai = Pegawai::forOrganization((int) $presensi->organization_id)->find($presensi->id_pegawai);
            $user = $pegawai ? User::where('role', User::ROLE_PEGAWAI)
                ->where('organization_id', $presensi->organization_id)
                ->where('email', $pegawai->email)->first() : null;
            if ($user) {
                $this->service()->notify($user, (int) $presensi->organization_id, 'presensi:approved:'.$presensi->id,
                    'presensi', 'Presensi disetujui', 'Presensi Anda telah disetujui oleh admin.', route('pegawai.presensi.index'));
            }
        }
    }

    private function service(): FinusNotificationService
    {
        return app(FinusNotificationService::class);
    }
}
