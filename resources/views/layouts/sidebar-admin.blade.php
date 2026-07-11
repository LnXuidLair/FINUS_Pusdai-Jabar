<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
:root{
  --sb-bg-1:#020617;
  --sb-bg-2:#020617;
  --sb-hover:#1e293b;
  --sb-active:#38bdf8;
  --sb-text:#e5e7eb;
  --sb-muted:#94a3b8;
}

.sidebar{
  background:linear-gradient(180deg,var(--sb-bg-1),var(--sb-bg-2));
  box-shadow:10px 0 40px rgba(0,0,0,.45);
  width:260px;
}

.sidebar .logo{
  padding:22px 16px 16px;
  text-align:center;
  border-bottom:1px solid rgba(148,163,184,.12);
}

.sidebar .logo img{
  max-width:130px;
}

.sidebar .logo-title{
  margin-top:8px;
  font-size:14px;
  font-weight:700;
  letter-spacing:2px;
  color:#e5e7eb;
}

.sidebar ul{
  padding:14px 10px 30px;
}

.sidebar .label{
  margin:18px 14px 8px;
  font-size:11px;
  letter-spacing:.15em;
  text-transform:uppercase;
  color:var(--sb-muted);
}

.sidebar a{
  display:flex;
  align-items:center;
  gap:12px;
  padding:11px 14px;
  margin:6px 6px;
  border-radius:12px;
  color:var(--sb-text);
  font-weight:500;
  font-size:14px;
  transition:.25s ease;
}

.sidebar a i{
  width:20px;
  text-align:center;
  font-size:15px;
  color:var(--sb-muted);
}

.sidebar a:hover{
  background:var(--sb-hover);
  transform:translateX(4px);
}

.sidebar a:hover i{
  color:var(--sb-active);
}

.sidebar a.active{
  background:linear-gradient(90deg, rgba(56,189,248,.25), rgba(56,189,248,.05));
  box-shadow: inset 4px 0 0 var(--sb-active);
  color:#fff;
}

.sidebar a.active i{
  color:var(--sb-active);
}
</style>

<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures">
  <div class="nano">
    <div class="nano-content">

      <div class="logo">
        <img src="{{ asset('assets/images/pusdai_dashboard.png') }}" alt="FINUS"
          onerror="this.style.display='none'">
        <div class="logo-title">FINUS</div>
        <div class="logo-sub-title">Keuangan Masjid</div>
      </div>

      <ul>
        <li class="label">Dashboard</li>
        <li>
          <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Dashboard
          </a>
        </li>

        <li class="label">Master Data</li>
        <li>
          <a href="{{ route('admin.pegawai.index') }}" class="{{ request()->routeIs('admin.pegawai.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Pegawai
          </a>
        </li>
        <li>
          <a href="{{ route('admin.coa.index') }}" class="{{ request()->routeIs('admin.coa.*') ? 'active' : '' }}">
            <i class="fa-solid fa-book"></i> COA
          </a>
        </li>
        <li>
          <a href="{{ route('admin.gaji-jabatan.index') }}" class="{{ request()->routeIs('admin.gaji-jabatan.*') ? 'active' : '' }}">
            <i class="fa-solid fa-briefcase"></i> Gaji & Jabatan
          </a>
        </li>

        <li class="label">Transaksi</li>
        <li>
          <a href="{{ route('admin.penggajian.index') }}" class="{{ request()->routeIs('admin.penggajian.*') ? 'active' : '' }}">
            <i class="fa-solid fa-wallet"></i> Penggajian
          </a>
        </li>
        <li>
          <a href="{{ route('admin.pengeluaran.index') }}" class="{{ request()->routeIs('admin.pengeluaran.*') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt"></i> Pengeluaran
          </a>
        </li>

        <li class="label">Laporan</li>
        <li>
          <a href="{{ route('admin.presensi.index') }}" class="{{ request()->routeIs('admin.presensi.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-check"></i> Presensi
          </a>
        </li>
        <li>
          <a href="{{ route('admin.laporan.jurnal-umum') }}" class="{{ request()->routeIs('admin.laporan.jurnal-umum') ? 'active' : '' }}">
            <i class="fa-solid fa-book-open"></i> Jurnal Umum
          </a>
        </li>
        <li>
          <a href="{{ route('admin.laporan.arus-kas') }}" class="{{ request()->routeIs('admin.laporan.arus-kas') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i> Laporan Keuangan
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>