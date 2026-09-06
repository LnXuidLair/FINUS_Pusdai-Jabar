@extends('layouts.app')

@section('title', 'Profil Akun')
@section('hide-page-header', '1')

@section('content')
@include('layouts.partials.finus-ui')

@php
    $displayName = $pegawai?->nama_pegawai ?: $user->name;
    $initial = mb_strtoupper(mb_substr(trim($displayName ?: 'U'), 0, 1));
    $roleLabel = match($user->role) {
        \App\Models\User::ROLE_ADMIN => 'Administrator',
        \App\Models\User::ROLE_PEGAWAI => 'Pegawai',
        \App\Models\User::ROLE_JAMAAH => 'Jamaah',
        default => ucfirst((string) $user->role),
    };
    $displayEmail = in_array($user->role, [
        \App\Models\User::ROLE_ADMIN,
        \App\Models\User::ROLE_PEGAWAI,
    ], true)
        ? strtolower((string) $user->email)
        : $user->email;
@endphp

<div class="fmu-page account-profile-page">
    <section class="fmu-hero account-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-user"></i></span>
            <div>
                <h1>Profil Saya</h1>
                <p>
                    @if($user->isAdmin())
                        Kelola nama yang tampil pada akun Admin FINUS.
                    @else
                        Lihat informasi identitas akun yang sedang digunakan di FINUS.
                    @endif
                </p>
            </div>
        </div>
    </section>

    <div class="account-profile-grid">
        <aside class="fmu-card account-identity-card">
            <div class="account-identity-top">
                <span class="account-profile-avatar">{{ $initial }}</span>
                <h2>{{ $displayName }}</h2>
                <p>{{ $pegawai?->jabatan ?: $roleLabel }}</p>
                <span class="account-active-badge">
                    <i class="fa-solid fa-circle-check"></i>
                    Akun Aktif
                </span>
            </div>

            <div class="account-identity-details">
                <div class="account-identity-row">
                    <span><i class="fa-solid fa-envelope"></i></span>
                    <div><small>Email</small><strong>{{ $displayEmail }}</strong></div>
                </div>
                <div class="account-identity-row">
                    <span><i class="fa-solid fa-shield-halved"></i></span>
                    <div><small>Jenis Akun</small><strong>{{ $roleLabel }}</strong></div>
                </div>
                <div class="account-identity-row">
                    <span><i class="fa-solid fa-circle-check"></i></span>
                    <div><small>Status Email</small><strong>{{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi' }}</strong></div>
                </div>
            </div>
        </aside>

        <section class="fmu-card account-profile-main">
            <div class="fmu-card-head">
                <div class="fmu-card-head-main">
                    <span class="fmu-card-icon"><i class="fa-solid fa-address-card"></i></span>
                    <div>
                        <h2>Informasi Profil</h2>
                        <p>
                            @if($user->isAdmin())
                                Nama Admin dapat diubah, sedangkan email login tetap menggunakan alamat yang terdaftar.
                            @else
                                Informasi utama yang terhubung dengan akun {{ $portalLabel }}.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if($user->isAdmin())
                <form method="POST" action="{{ route('admin.profile.update') }}">
                    @csrf
                    @method('PATCH')
            @endif

            <div class="fmu-card-body">
                @if($user->isAdmin())
                    <div class="account-profile-note">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Perubahan nama hanya memengaruhi nama yang tampil di FINUS. Email login tidak ikut berubah.</span>
                    </div>
                @endif

                <div class="account-profile-form-grid">
                    <div class="fmu-field">
                        <label class="fmu-label" for="profile-name">Nama</label>
                        <div class="fmu-input-icon-wrap">
                            <i class="fa-solid fa-user"></i>
                            <input
                                id="profile-name"
                                class="fmu-control @error('name') is-invalid @enderror"
                                @if($user->isAdmin())
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    maxlength="255"
                                    autocomplete="name"
                                    required
                                @else
                                    value="{{ $displayName }}"
                                    readonly
                                @endif
                            >
                        </div>
                        @error('name')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="fmu-field">
                        <label class="fmu-label">Email</label>
                        <div class="fmu-input-icon-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input class="fmu-control" value="{{ $displayEmail }}" readonly>
                        </div>
                    </div>

                    <div class="fmu-field">
                        <label class="fmu-label">Jenis Akun</label>
                        <div class="fmu-input-icon-wrap">
                            <i class="fa-solid fa-shield-halved"></i>
                            <input class="fmu-control" value="{{ $roleLabel }}" readonly>
                        </div>
                    </div>

                    <div class="fmu-field">
                        <label class="fmu-label">Status Email</label>
                        <div class="fmu-input-icon-wrap">
                            <i class="fa-solid fa-circle-check"></i>
                            <input class="fmu-control" value="{{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi' }}" readonly>
                        </div>
                    </div>

                    @if($pegawai)
                        <div class="fmu-field">
                            <label class="fmu-label">NIP</label>
                            <div class="fmu-input-icon-wrap">
                                <i class="fa-solid fa-id-card"></i>
                                <input class="fmu-control" value="{{ $pegawai->nip ?: '-' }}" readonly>
                            </div>
                        </div>

                        <div class="fmu-field">
                            <label class="fmu-label">Jabatan</label>
                            <div class="fmu-input-icon-wrap">
                                <i class="fa-solid fa-briefcase"></i>
                                <input class="fmu-control" value="{{ $pegawai->jabatan ?: '-' }}" readonly>
                            </div>
                        </div>

                        <div class="fmu-field">
                            <label class="fmu-label">Nomor Telepon</label>
                            <div class="fmu-input-icon-wrap">
                                <i class="fa-solid fa-phone"></i>
                                <input class="fmu-control" value="{{ $pegawai->no_telp ?: '-' }}" readonly>
                            </div>
                        </div>

                        <div class="fmu-field">
                            <label class="fmu-label">Jenis Kelamin</label>
                            <div class="fmu-input-icon-wrap">
                                <i class="fa-solid fa-venus-mars"></i>
                                <input class="fmu-control" value="{{ $pegawai->gender_label }}" readonly>
                            </div>
                        </div>

                        <div class="fmu-field account-profile-full">
                            <label class="fmu-label">Alamat</label>
                            <textarea class="fmu-textarea" readonly>{{ $pegawai->alamat ?: '-' }}</textarea>
                        </div>
                    @endif
                </div>
            </div>

            @if($user->isAdmin())
                <div class="account-profile-actions">
                    <button type="submit" class="fmu-btn fmu-btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan Nama
                    </button>
                </div>
                </form>
            @endif
        </section>
    </div>
</div>
@endsection

@push('styles')
<style data-account-profile-ui>
.account-profile-grid{display:grid;grid-template-columns:minmax(260px,.48fr) minmax(0,1.52fr);gap:18px;align-items:start}
.account-identity-card,.account-profile-main{box-shadow:0 16px 38px rgba(15,23,42,.065)}
.account-identity-card{overflow:hidden}
.account-identity-top{display:flex;flex-direction:column;align-items:center;padding:28px 22px 24px;text-align:center;background:linear-gradient(180deg,#FFFFFF,#FBFDFC)}
.account-profile-avatar{display:inline-flex;align-items:center;justify-content:center;width:88px;height:88px;border-radius:26px;background:linear-gradient(135deg,#0E5423,#22BA51);color:#fff;font-size:32px;font-weight:900;box-shadow:0 14px 28px rgba(14,84,35,.18)}
.account-identity-top h2{margin:16px 0 4px;color:#173B24;font-size:20px;font-weight:900;line-height:1.35;word-break:break-word}
.account-identity-top p{margin:0;color:#6C7C72;font-size:12.5px}
.account-active-badge{display:inline-flex;align-items:center;gap:7px;margin-top:15px;padding:7px 11px;border:1px solid #CDE8D4;border-radius:999px;background:#EAF8EE;color:#15933C;font-size:10.5px;font-weight:900}
.account-identity-details{display:grid;padding:7px 20px 13px;border-top:1px solid #E5ECE7}
.account-identity-row{display:flex;align-items:center;gap:11px;padding:13px 0;border-bottom:1px solid #EDF2EE}
.account-identity-row:last-child{border-bottom:0}
.account-identity-row>span{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:10px;background:#EAF8EE;color:#179B40;flex:0 0 auto}
.account-identity-row small{display:block;margin-bottom:2px;color:#819087;font-size:10px;font-weight:750}
.account-identity-row strong{display:block;color:#294536;font-size:11.5px;font-weight:850;line-height:1.35;word-break:break-word}
.account-profile-note{display:flex;align-items:flex-start;gap:9px;margin-bottom:20px;padding:12px 14px;border:1px solid #D7E9DC;border-radius:12px;background:#F0F8F2;color:#526C5A;font-size:11.5px;line-height:1.55}
.account-profile-note i{margin-top:2px;color:#179B40}
.account-profile-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:19px 20px}
.account-profile-full{grid-column:1/-1}
.account-profile-actions{display:flex;justify-content:flex-end;padding:17px 24px;border-top:1px solid #E1EAE4;background:#FBFDFC}
@media(max-width:991.98px){.account-profile-grid{grid-template-columns:1fr}.account-identity-card{display:grid;grid-template-columns:minmax(220px,.75fr) minmax(0,1.25fr)}.account-identity-details{border-top:0;border-left:1px solid #E5ECE7}}
@media(max-width:767.98px){.account-identity-card{display:block}.account-identity-details{border-left:0;border-top:1px solid #E5ECE7}.account-profile-form-grid{grid-template-columns:1fr}.account-profile-full{grid-column:auto}.account-profile-actions{padding:16px 20px}.account-profile-actions .fmu-btn{width:100%}}
</style>
@endpush

@push('dark-styles')
<style data-finus-dark-local="account/profile.blade.php">
html[data-finus-theme="dark"] .account-profile-page{color:#EEF7F1}
html[data-finus-theme="dark"] .account-profile-page .fmu-card{border-color:#264033!important;background:#0D1D14!important;box-shadow:0 18px 42px rgba(0,0,0,.16)!important}
html[data-finus-theme="dark"] .account-profile-page .fmu-card-head{border-color:#263D31!important;background:#102219!important}
html[data-finus-theme="dark"] .account-profile-page .fmu-card-head h2{color:#F3F8F4!important}
html[data-finus-theme="dark"] .account-profile-page .fmu-card-head p{color:#9CB0A3!important}
html[data-finus-theme="dark"] .account-profile-page .fmu-card-icon{background:#123B20!important;color:#63DF82!important}
html[data-finus-theme="dark"] .account-identity-top{background:#102219}
html[data-finus-theme="dark"] .account-identity-top h2{color:#F3F8F4}
html[data-finus-theme="dark"] .account-identity-top p{color:#9DAFA4}
html[data-finus-theme="dark"] .account-active-badge{border-color:#2C5A38;background:#13391F;color:#79E391}
html[data-finus-theme="dark"] .account-identity-details{border-color:#284033;background:#0D1D14}
html[data-finus-theme="dark"] .account-identity-row{border-color:#263C31}
html[data-finus-theme="dark"] .account-identity-row>span{background:#123B20;color:#69E289}
html[data-finus-theme="dark"] .account-identity-row small{color:#879A8D}
html[data-finus-theme="dark"] .account-identity-row strong{color:#E6F0E9}
html[data-finus-theme="dark"] .account-profile-note{border-color:#285037;background:#102B1A;color:#A8BCAD}
html[data-finus-theme="dark"] .account-profile-page .fmu-label{color:#DDE9E1!important}
html[data-finus-theme="dark"] .account-profile-page .fmu-control,html[data-finus-theme="dark"] .account-profile-page .fmu-textarea{border-color:#315040!important;background:#09170F!important;color:#EFF8F1!important}
html[data-finus-theme="dark"] .account-profile-page .fmu-control[readonly],html[data-finus-theme="dark"] .account-profile-page .fmu-textarea[readonly]{background:#0A1810!important;color:#D0DED4!important}
html[data-finus-theme="dark"] .account-profile-page .fmu-input-icon-wrap>i{color:#67DE83!important}
html[data-finus-theme="dark"] .account-profile-actions{border-color:#284033;background:#102219}
</style>
@endpush
