<?php

namespace App\Services;

use App\Events\FinusNotificationCreated;
use App\Models\FinusNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Pusher\PushNotifications\PushNotifications;

class FinusNotificationService
{
    /**
     * Idempotent, per-recipient delivery: one database record per event_key.
     * External provider errors cannot roll back genuine finance/attendance records.
     */
    public function notify(User $user, int $organizationId, string $key, string $category, string $title, string $message, string $url): void
    {
        try {
            if ($user->organization_id && (int) $user->organization_id !== $organizationId) {
                return;
            }

            $created = DB::table('finus_notifications')->insertOrIgnore([
                'user_id' => $user->id,
                'organization_id' => $organizationId,
                'event_key' => $key,
                'category' => $category,
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($created !== 1) {
                return;
            }
        } catch (\Throwable $exception) {
            Log::error('FINUS notification could not be stored', ['event' => $key, 'error' => $exception->getMessage()]);
            return;
        }

        $counts = $this->unreadCounts($user);
        try {
            if (config('broadcasting.default') === 'pusher') {
                broadcast(new FinusNotificationCreated((int) $user->id, ['counts' => $counts]));
            }
        } catch (\Throwable $exception) {
            Log::warning('FINUS Channels push failed; notification retained', ['event' => $key, 'error' => $exception->getMessage()]);
        }

        try {
            if (filled(config('finus_notifications.beams.instance_id')) && filled(config('finus_notifications.beams.secret_key')) && class_exists(PushNotifications::class)) {
                $beams = new PushNotifications([
                    'instanceId' => config('finus_notifications.beams.instance_id'),
                    'secretKey' => config('finus_notifications.beams.secret_key'),
                ]);
                // One Beams identity per FINUS account, reusable on multiple devices.
                // Keep in sync with FinusNotificationController::beamsToken()
                // and accountId in resources/js/finus-notifications.js.
                $beamsUserId = 'finus-'.$user->role.'-'.$user->id;
                $beams->publishToUsers([$beamsUserId], [
                    'web' => ['notification' => [
                        'title' => $title,
                        // No donor/employee personal information on device lock screen.
                        'body' => 'Ada aktivitas baru di FINUS. Buka aplikasi untuk melihat detail.',
                        'deep_link' => $url,
                    ]],
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('FINUS Beams push failed; notification retained', ['event' => $key, 'error' => $exception->getMessage()]);
        }
    }

    public function notifyAdmins(int $organizationId, string $key, string $category, string $title, string $message, string $url): void
    {
        if ($organizationId < 1) {
            return;
        }

        User::query()->where('role', User::ROLE_ADMIN)->where('organization_id', $organizationId)
            ->select(['id', 'organization_id', 'role'])->chunkById(100, function ($users) use ($organizationId, $key, $category, $title, $message, $url): void {
                foreach ($users as $user) {
                    $this->notify($user, $organizationId, $key, $category, $title, $message, $url);
                }
            });
    }

    public function unreadCounts(User $user): array
    {
        $zero = ['total' => 0, 'presensi' => 0, 'ziswaf' => 0, 'pemasukan' => 0, 'pengeluaran' => 0];
        try {
            if (! Schema::hasTable('finus_notifications')) {
                return $zero;
            }
            $query = FinusNotification::query()->where('user_id', $user->id)->whereNull('read_at');
            if ($user->organization_id) {
                $query->where('organization_id', $user->organization_id);
            }
            $counts = (clone $query)->selectRaw('category, count(*) as total')->groupBy('category')->pluck('total', 'category');
            foreach ($counts as $category => $total) {
                if (array_key_exists($category, $zero)) {
                    $zero[$category] = (int) $total;
                }
                $zero['total'] += (int) $total;
            }
        } catch (\Throwable $exception) {
            Log::warning('FINUS notification counts unavailable', ['error' => $exception->getMessage()]);
        }
        return $zero;
    }
}