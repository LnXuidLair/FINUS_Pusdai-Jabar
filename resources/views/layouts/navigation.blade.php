@php
    $navUser = $currentUser;
    $roleLabel = $navUser?->isAdmin()
        ? 'Admin'
        : ($navUser?->isPegawai() ? 'Pegawai' : 'Jamaah');
    $rawPageContext = trim($__env->yieldContent('title'));
    $pageContext = $rawPageContext !== ''
        ? html_entity_decode($rawPageContext, ENT_QUOTES | ENT_HTML5, 'UTF-8')
        : 'FINUS';
    $initial = mb_strtoupper(mb_substr(trim($navUser?->name ?? 'F'), 0, 1));
    $navEmail = in_array($navUser?->role, [
        \App\Models\User::ROLE_ADMIN,
        \App\Models\User::ROLE_PEGAWAI,
    ], true)
        ? strtolower((string) $navUser?->email)
        : $navUser?->email;

    $profileRoute = match (true) {
        $navUser?->isAdmin() => 'admin.profile',
        $navUser?->isPegawai() => 'pegawai.profile',
        default => 'jamaah.profile',
    };

    $settingsRoute = match (true) {
        $navUser?->isAdmin() => 'admin.settings',
        $navUser?->isPegawai() => 'pegawai.settings',
        default => 'jamaah.settings',
    };
@endphp
<style>
    .header.finus-topbar {
        position: fixed !important;
        top: 0;
        right: 0;
        left: var(--finus-sidebar-width);
        z-index: 1035;
        height: var(--finus-header-height);
        min-height: var(--finus-header-height);
        margin: 0 !important;
        padding: 0 !important;
        background: linear-gradient(to right, #0FB442 0%, #1AAF48 39%, #118635 75%, #004716 100%) !important;
        border: 0 !important;
        box-shadow: 0 10px 28px rgba(0,71,22,.20) !important;
        transition: left .25s ease;
    }
    body.sidebar-collapsed .header.finus-topbar { left: 0; }
    .finus-topbar-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        width: 100%;
        height: 100%;
        padding: 0 clamp(14px,2vw,26px);
    }
    .finus-topbar-left,
    .finus-topbar-user {
        display: flex;
        align-items: center;
        min-width: 0;
    }
    .finus-topbar-left {
        gap: 13px;
        height: 100%;
        align-items: center !important;
    }
    /* Reset style bawaan template agar tombol hamburger benar-benar sejajar. */
    button.finus-topbar-toggle {
        position: static !important;
        inset: auto !important;
        float: none !important;
        margin: 0 !important;
        line-height: 1 !important;
        transform: none !important;
    }
    button.finus-topbar-toggle:hover,
    button.finus-topbar-toggle:focus-visible {
        transform: none !important;
    }
    .finus-topbar-toggle > span {
        display: flex !important;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        width: 100%;
        height: 100%;
        line-height: 1;
    }
    .finus-topbar-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        min-width: 42px;
        height: 42px;
        padding: 0;
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 12px;
        background: rgba(255,255,255,.13);
        color: #fff;
        cursor: pointer;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.12);
        transition: .2s ease;
    }
    .finus-topbar-toggle:hover,
    .finus-topbar-toggle:focus-visible {
        background: rgba(255,255,255,.21);
        outline: none;
        transform: translateY(-1px);
    }
    .finus-topbar-toggle .line {
        display: block !important;
        width: 20px !important;
        height: 2px !important;
        margin: 0 !important;
        border-radius: 99px;
        background: currentColor;
    }
    .finus-topbar-context {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
        min-height: 42px;
        line-height: 1.15;
    }
    .finus-topbar-eyebrow {
        display: block;
        color: rgba(255,255,255,.72);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .13em;
        text-transform: uppercase;
    }
    .finus-topbar-title {
        display: block;
        max-width: 480px;
        margin-top: 3px;
        line-height: 1.25;
        overflow: hidden;
        color: #fff;
        font-size: 15px;
        font-weight: 900;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .finus-topbar-user {
        gap: 10px;
        min-height: 44px;
        padding: 5px 8px 5px 6px;
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 13px;
        background: rgba(255,255,255,.94);
        color: #123D20;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(0,57,18,.15);
        transition: .2s ease;
    }
    .finus-topbar-user:hover,
    .finus-topbar-user[aria-expanded="true"] {
        background: #fff;
        transform: translateY(-1px);
    }
    .finus-user-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 33px;
        min-width: 33px;
        height: 33px;
        border-radius: 10px;
        background: linear-gradient(135deg,#EAF8EE,#D9F2DF);
        color: #0E6E2C;
        font-size: 13px;
        font-weight: 900;
    }
    .finus-user-copy { display: grid; min-width: 0; line-height: 1.2; }
    .finus-user-copy strong {
        max-width: 175px;
        overflow: hidden;
        color: #183D24;
        font-size: 11.5px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .finus-user-copy small { margin-top: 3px; color: #718078; font-size: 9.5px; font-weight: 700; }
    .finus-user-chevron { color: #6C7D72; font-size: 10px; }
    .finus-user-menu {
        width: min(290px, calc(100vw - 24px));
        margin-top: 9px;
        padding: 8px;
        border: 1px solid #DFE9E2;
        border-radius: 15px;
        box-shadow: 0 20px 48px rgba(15,23,42,.14);
    }
    .finus-user-menu-head {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 11px;
        border-radius: 11px;
        background: #F4FAF6;
    }
    .finus-user-menu-head .finus-user-avatar { width: 39px; min-width: 39px; height: 39px; }
    .finus-user-menu-head strong { display: block; color: #1C3424; font-size: 12px; }
    .finus-user-menu-head small { display: block; margin-top: 3px; color: #718078; font-size: 10px; word-break: break-word; }
    .finus-user-menu-section {
        display: grid;
        gap: 4px;
    }

    .finus-account-link {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 48px;
        padding: 7px 10px;
        border-radius: 11px;
        color: #294334;
        text-decoration: none !important;
        transition: background .18s ease, color .18s ease, transform .18s ease;
    }

    .finus-account-link:hover,
    .finus-account-link:focus-visible {
        background: #F1F8F3;
        color: #0E6E2C;
        outline: none;
        transform: translateX(1px);
    }

    .finus-account-link.is-active {
        background: #EAF8EE;
        color: #0E6E2C;
    }

    .finus-account-link-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        min-width: 32px;
        height: 32px;
        border-radius: 9px;
        background: #ECF6EF;
        color: #179B40;
        font-size: 12px;
    }

    .finus-account-link.is-active .finus-account-link-icon {
        background: #D9F2DF;
        color: #0E6E2C;
    }

    .finus-account-link-copy {
        display: grid;
        min-width: 0;
        flex: 1;
        line-height: 1.2;
    }

    .finus-account-link-copy strong {
        color: inherit;
        font-size: 11.5px;
        font-weight: 850;
    }

    .finus-account-link-copy small {
        margin-top: 3px;
        overflow: hidden;
        color: #78877E;
        font-size: 9.5px;
        font-weight: 650;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .finus-account-link-chevron {
        color: #9AA79F;
        font-size: 9px;
    }

    .finus-logout-button {
        display: flex;
        align-items: center;
        gap: 9px;
        width: 100%;
        min-height: 42px;
        padding: 0 11px;
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: #C4313B;
        font-size: 11.5px;
        font-weight: 850;
        cursor: pointer;
    }
    .finus-logout-button:hover { background: #FFF1F2; }
    @media (max-width: 991.98px) {
        .header.finus-topbar { left: 0; }
        .finus-topbar-context { display: none; }
    }
    @media (max-width: 520px) {
        .finus-user-copy { display: none; }
        .finus-topbar-user { padding-right: 7px; }
        .finus-user-chevron { display: none; }
    }
</style>
<header class="header finus-topbar" aria-label="Header aplikasi">
    <div class="finus-topbar-inner">
        <div class="finus-topbar-left">
            <button type="button" class="hamburger sidebar-toggle finus-topbar-toggle" aria-label="Buka atau tutup menu navigasi" aria-controls="finusSidebar">
                <span aria-hidden="true">
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                </span>
            </button>
            <div class="finus-topbar-context">
                <span class="finus-topbar-eyebrow">{{ $roleLabel }} FINUS</span>
                <span class="finus-topbar-title">{{ $pageContext }}</span>
            </div>
        </div>
        <div class="dropdown">
            <button type="button" class="finus-topbar-user dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="finus-user-avatar" aria-hidden="true">{{ $initial }}</span>
                <span class="finus-user-copy">
                    <strong>{{ $navUser->first_name }}</strong>
                    <small>{{ $roleLabel }}</small>
                </span>
                <i class="fa-solid fa-chevron-down finus-user-chevron" aria-hidden="true"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right finus-user-menu">
                <div class="finus-user-menu-head">
                    <span class="finus-user-avatar" aria-hidden="true">{{ $initial }}</span>
                    <span>
                        <strong>{{ $navUser->name }}</strong>
                        <small>{{ $navEmail }}</small>
                    </span>
                </div>
                <div class="dropdown-divider"></div>

                <div class="finus-user-menu-section" aria-label="Pengaturan akun">
                    <a
                        href="{{ route($profileRoute) }}"
                        class="finus-account-link {{ request()->routeIs($profileRoute) ? 'is-active' : '' }}"
                        @if(request()->routeIs($profileRoute)) aria-current="page" @endif
                    >
                        <span class="finus-account-link-icon" aria-hidden="true">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span class="finus-account-link-copy">
                            <strong>Profil Saya</strong>
                            <small>Lihat informasi akun Anda</small>
                        </span>
                        <i class="fa-solid fa-chevron-right finus-account-link-chevron" aria-hidden="true"></i>
                    </a>

                    <a
                        href="{{ route($settingsRoute) }}"
                        class="finus-account-link {{ request()->routeIs($settingsRoute) ? 'is-active' : '' }}"
                        @if(request()->routeIs($settingsRoute)) aria-current="page" @endif
                    >
                        <span class="finus-account-link-icon" aria-hidden="true">
                            <i class="fa-solid fa-gear"></i>
                        </span>
                        <span class="finus-account-link-copy">
                            <strong>Pengaturan</strong>
                            <small>Keamanan dan pengaturan akun</small>
                        </span>
                        <i class="fa-solid fa-chevron-right finus-account-link-chevron" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="dropdown-divider"></div>

                <form method="POST" action="{{ $logoutRoute }}">
                    @csrf
                    <button class="finus-logout-button" type="submit">
                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                        Keluar dari akun {{ $roleLabel }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>