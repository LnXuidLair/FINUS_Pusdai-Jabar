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

<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-user"></i></span>
            <div>
                <h1>Profil Saya</h1>
                <p>
                    @if($user->isAdmin())
                        Perbarui nama Admin tanpa mengubah email login FINUS.
                    @else
                        Lihat informasi akun dan identitas yang sedang digunakan di FINUS.
                    @endif
                </p>
            </div>
        </div>
        <div class="fmu-hero-actions">
            <a href="{{ route($settingsRoute) }}" class="fmu-btn" style="background:#fff;color:#0E5423!important">
                <i class="fa-solid fa-gear"></i>
                Pengaturan
            </a>
        </div>
    </section>

    <div class="fmu-grid" style="grid-template-columns:minmax(250px,.55fr) minmax(0,1.45fr);align-items:start">
        <aside class="fmu-card">
            <div class="fmu-card-body text-center">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:92px;height:92px;border-radius:28px;background:linear-gradient(135deg,#0E5423,#22BA51);color:#fff;font-size:34px;font-weight:900;box-shadow:0 12px 28px rgba(14,84,35,.18)">
                    {{ $initial }}
                </span>
                <h2 style="margin:16px 0 4px;font-size:21px;font-weight:900;color:#173b24">{{ $displayName }}</h2>
                <p style="margin:0;color:#66776c">{{ $pegawai?->jabatan ?: $roleLabel }}</p>
                <div style="margin-top:16px">
                    <span class="fmu-badge" style="--badge-color:#179B40;--badge-soft:#EAF8EE">
                        <i class="fa-solid fa-circle-check"></i>
                        Akun Aktif
                    </span>
                </div>
            </div>
        </aside>

        <section class="fmu-card">
            <div class="fmu-card-head">
                <div class="fmu-card-head-main">
                    <span class="fmu-card-icon"><i class="fa-solid fa-address-card"></i></span>
                    <div>
                        <h2>Informasi Profil</h2>
                        <p>
                            @if($user->isAdmin())
                                Nama Admin dapat diubah. Email login tetap menggunakan admin@pusdai.finus.id.
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
                <div class="fmu-form-grid">
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
                        @if($user->isAdmin())
                            <span class="fmu-help">Perubahan nama hanya memengaruhi nama yang tampil di FINUS. Email login tidak ikut berubah.</span>
                            @error('name')<span class="fmu-error">{{ $message }}</span>@enderror
                        @endif
                    </div>

                    <div class="fmu-field">
                        <label class="fmu-label">Email</label>
                        <div class="fmu-input-icon-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input class="fmu-control" value="{{ $displayEmail }}" readonly>
                        </div>
                        @if($user->isAdmin())
                            <span class="fmu-help">Email Admin di-hardcode oleh FINUS dan tidak dapat diubah dari Profil.</span>
                        @endif
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

                        <div class="fmu-field fmu-field-full">
                            <label class="fmu-label">Alamat</label>
                            <textarea class="fmu-textarea" readonly>{{ $pegawai->alamat ?: '-' }}</textarea>
                        </div>
                    @endif
                </div>
            </div>

            <div class="fmu-actions">
                <a href="{{ route($dashboardRoute) }}" class="fmu-btn">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Dashboard
                </a>

                @if($user->isAdmin())
                    <button type="submit" class="fmu-btn fmu-btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan Nama
                    </button>
                @else
                    <a href="{{ route($settingsRoute) }}" class="fmu-btn fmu-btn-primary">
                        <i class="fa-solid fa-gear"></i>
                        Buka Pengaturan
                    </a>
                @endif
            </div>

            @if($user->isAdmin())
                </form>
            @endif
        </section>
    </div>
</div>
@endsection
