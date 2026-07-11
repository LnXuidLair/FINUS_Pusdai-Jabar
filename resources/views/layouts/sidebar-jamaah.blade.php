<style>
.sidebar.jamaah-sidebar {
    background:linear-gradient(180deg,#065f22,#0f8a3c);
    color:#fff;
}
.jamaah-sidebar ul { list-style:none; padding:14px 10px 30px; margin:0; }
.jamaah-sidebar .logo { padding:22px 16px; text-align:center; }
.jamaah-sidebar .menu-label { padding:14px; color:rgba(255,255,255,.65); font-size:11px; text-transform:uppercase; letter-spacing:.14em; }
.jamaah-sidebar a { display:flex; align-items:center; gap:12px; padding:11px 14px; margin:5px; border-radius:10px; color:#fff; }
.jamaah-sidebar a:hover, .jamaah-sidebar a.active { background:rgba(255,255,255,.15); color:#fff; }
</style>
<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures jamaah-sidebar">
    <div class="nano">
        <div class="nano-content">
            <div class="logo">
                <img src="{{ asset('assets/images/pusdai_dashboard.png') }}" alt="FINUS" style="max-width:130px"
                    onerror="this.style.display='none'">
                <div class="mt-2 font-weight-bold">FINUS JAMAAH</div>
            </div>
            <ul>
                <li class="menu-label">Jamaah</li>

                <li>
                    <a href="{{ route('jamaah.dashboard') }}"
                        class="{{ request()->routeIs('jamaah.dashboard') ? 'active' : '' }}">
                        <i class="ti-home"></i> Beranda
                    </a>
                </li>

                <li>
                    <a href="{{ route('jamaah.transaksi.create', 'zakat') }}"
                        class="{{ request()->routeIs('jamaah.transaksi.*') && request()->route('jenis') === 'zakat' ? 'active' : '' }}">
                        <i class="fa fa-hand-holding-heart"></i> Transaksi Zakat
                    </a>
                </li>

                <li>
                    <a href="{{ route('jamaah.transaksi.create', 'infak') }}"
                        class="{{ request()->routeIs('jamaah.transaksi.*') && request()->route('jenis') === 'infak' ? 'active' : '' }}">
                        <i class="fa fa-donate"></i> Transaksi Infak
                    </a>
                </li>

                <li>
                    <a href="{{ route('jamaah.transaksi.create', 'wakaf') }}"
                        class="{{ request()->routeIs('jamaah.transaksi.*') && request()->route('jenis') === 'wakaf' ? 'active' : '' }}">
                        <i class="fa fa-mosque"></i> Transaksi Wakaf
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>