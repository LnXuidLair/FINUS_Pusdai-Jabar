@php
    // Diisi pada sidebar induk; partial cukup membaca nilainya.
    $finusCounts = $finusCounts ?? ['total' => 0, 'presensi' => 0, 'ziswaf' => 0, 'pemasukan' => 0, 'pengeluaran' => 0];
    $finusPortal = $currentGuard;
@endphp
<li>
    <a href="{{ route('finus.notifications.'.$finusPortal.'.index') }}" class="{{ request()->routeIs('finus.notifications.*.index') ? 'active' : '' }}">
        <i class="fa-solid fa-bell" aria-hidden="true"></i>
        <span>Notifikasi</span>
        <span class="finus-notification-badge" data-finus-badge="total" @if(!$finusCounts['total']) hidden @endif>{{ $finusCounts['total'] }}</span>
    </a>
</li>