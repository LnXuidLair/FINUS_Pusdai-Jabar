@extends('layouts.app')
@section('title', 'Notifikasi')
@section('content')
<section class="finus-notifications-page">
    <div class="finus-notifications-heading">
        <div>
            <h1>Notifikasi FINUS</h1>
            <p>Aktivitas akun dan masjid Anda.</p>
        </div>
        <form method="POST" action="{{ route('finus.notifications.'.$guard.'.read-all') }}">
            @csrf
            <button class="finus-notification-action" type="submit">Tandai semua dibaca</button>
        </form>
    </div>

    <nav class="finus-notification-filters" aria-label="Filter status notifikasi">
        <a href="{{ route('finus.notifications.'.$guard.'.index') }}"
           class="finus-notification-filter {{ $statusFilter === 'all' ? 'is-active' : '' }}"
           @if($statusFilter === 'all') aria-current="page" @endif>Semua</a>
        <a href="{{ route('finus.notifications.'.$guard.'.index', ['status' => 'unread']) }}"
           class="finus-notification-filter {{ $statusFilter === 'unread' ? 'is-active' : '' }}"
           @if($statusFilter === 'unread') aria-current="page" @endif>Belum dibaca</a>
        <a href="{{ route('finus.notifications.'.$guard.'.index', ['status' => 'read']) }}"
           class="finus-notification-filter {{ $statusFilter === 'read' ? 'is-active' : '' }}"
           @if($statusFilter === 'read') aria-current="page" @endif>Sudah dibaca</a>
    </nav>

    @forelse ($notifications as $notification)
        <article class="finus-notification-item {{ $notification->read_at ? '' : 'finus-notification-unread' }}">
            <div>
                <strong>{{ $notification->title }}</strong>
                <small>{{ $notification->created_at->format('d M Y H:i') }}</small>
                <p>{{ $notification->message }}</p>
                @if($notification->url)
                    <a href="{{ $notification->url }}">Lihat detail</a>
                @endif
            </div>
            @if(!$notification->read_at)
                <form method="POST" action="{{ route('finus.notifications.'.$guard.'.read', $notification->id) }}">
                    @csrf
                    <button class="finus-notification-action" type="submit">Tandai dibaca</button>
                </form>
            @endif
        </article>
    @empty
        <p class="finus-notifications-empty">
            @if($statusFilter === 'unread')
                Tidak ada notifikasi yang belum dibaca.
            @elseif($statusFilter === 'read')
                Belum ada notifikasi yang sudah dibaca.
            @else
                Belum ada notifikasi.
            @endif
        </p>
    @endforelse
    {{ $notifications->links() }}
</section>
@endsection