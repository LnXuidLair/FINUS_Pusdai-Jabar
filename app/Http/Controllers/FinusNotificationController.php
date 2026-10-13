<?php

namespace App\Http\Controllers;

use App\Models\FinusNotification;
use App\Services\FinusNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\PushNotifications\PushNotifications;

class FinusNotificationController extends Controller
{
    private function actor(Request $request)
    {
        $guard = $request->route('finus_guard');
        abort_unless(in_array($guard, ['admin', 'pegawai', 'jamaah'], true), 403);
        $user = Auth::guard($guard)->user();
        abort_unless($user && $user->role === $guard, 403);
        if ($guard !== 'jamaah') {
            abort_unless((int) $user->organization_id > 0, 403);
        }
        return $user;
    }

    private function inbox(Request $request)
    {
        $user = $this->actor($request);
        $query = FinusNotification::query()->where('user_id', $user->id);
        if ($user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        }
        return $query;
    }

    public function index(Request $request)
    {
        $filter = (string) $request->query('status', 'all');
        if (! in_array($filter, ['all', 'unread', 'read'], true)) {
            $filter = 'all';
        }

        $query = $this->inbox($request)->latest('id');
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        return view('notifications.index', [
            'notifications' => $query->paginate(20)->appends($filter === 'all' ? [] : ['status' => $filter]),
            'statusFilter' => $filter,
            'guard' => $request->route('finus_guard'),
        ]);
    }

    public function feed(Request $request, FinusNotificationService $service)
    {
        $user = $this->actor($request);
        $query = $this->inbox($request)->latest('id');
        return response()->json(['counts' => $service->unreadCounts($user),
            'items' => $query->limit(8)
                ->get(['id', 'title', 'message', 'url', 'category', 'read_at', 'created_at'])]);
    }

    public function read(Request $request, int $notification)
    {
        $row = $this->inbox($request)->findOrFail($notification);
        if (! $row->read_at) {
            $row->update(['read_at' => now()]);
        }
        return back();
    }

    public function readAll(Request $request)
    {
        $this->inbox($request)->whereNull('read_at')->update(['read_at' => now()]);
        return back();
    }

    public function channelAuth(Request $request)
    {
        $user = $this->actor($request);
        $input = $request->validate([
            'socket_id' => ['required', 'string', 'regex:/^\d+\.\d+$/'],
            'channel_name' => ['required', 'string', 'max:120'],
        ]);
        $expected = 'private-finus.user.'.$user->id;
        abort_unless(hash_equals($expected, $input['channel_name']), 403);
        $key = config('broadcasting.connections.pusher.key');
        $secret = config('broadcasting.connections.pusher.secret');
        abort_unless(filled($key) && filled($secret), 503);
        $signature = hash_hmac('sha256', $input['socket_id'].':'.$input['channel_name'], $secret);
        return response()->json(['auth' => $key.':'.$signature]);
    }

    public function beamsToken(Request $request)
    {
        $user = $this->actor($request);
        $beamsUser = 'finus-'.$user->role.'-'.$user->id;
        abort_unless(hash_equals($beamsUser, (string) $request->query('user_id')), 403);
        $instance = config('finus_notifications.beams.instance_id');
        $secret = config('finus_notifications.beams.secret_key');
        abort_unless(filled($instance) && filled($secret) && class_exists(PushNotifications::class), 503);
        $beams = new PushNotifications(['instanceId' => $instance, 'secretKey' => $secret]);
        return response()->json($beams->generateToken($beamsUser));
    }
}