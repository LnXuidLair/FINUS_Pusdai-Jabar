<?php

use App\Http\Controllers\AccessCodeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GajiJabatanController;
use App\Http\Controllers\JamaahController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PegawaiDashboardController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\PresensiController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/login', fn () => redirect()->route('home'))->name('login');
Route::get('/register', fn () => redirect()->route('login.jamaah'));

Route::middleware('guest')->group(function () {
    Route::get('/login/admin', function () {
        return User::where('role', User::ROLE_ADMIN)->exists()
            ? view('auth.login-admin')
            : redirect()->route('register.admin');
    })->name('login.admin');

    Route::view('/login/pegawai', 'auth.login-staff')->name('login.staff');
    Route::view('/login/jamaah', 'auth.login-jamaah')->name('login.jamaah');

    Route::post('/login/admin', [LoginController::class, 'adminLogin']);
    Route::post('/login/pegawai', [LoginController::class, 'staffLogin']);
    Route::post('/login/jamaah', [LoginController::class, 'jamaahLogin']);

    Route::get('/register/admin', function () {
        if (User::where('role', User::ROLE_ADMIN)->exists()) {
            return redirect()->route('login.admin')
                ->withErrors(['name' => 'Admin sudah tersedia.']);
        }

        return view('auth.register-admin');
    })->name('register.admin');

    Route::get('/register/pegawai', [RegisterController::class, 'showStaffActivation'])
        ->name('register.staff');

    Route::post('/verifikasi/pegawai', [RegisterController::class, 'verifyStaff'])
        ->middleware('throttle:10,1')
        ->name('verify.staff');

    Route::get('/register/pegawai/akun', [RegisterController::class, 'showStaffAccountRegistration'])
        ->name('register.staff.account');

    Route::view('/register/jamaah', 'auth.register-jamaah')
        ->name('register.jamaah');

    Route::post('/register/admin', [RegisterController::class, 'registerAdmin'])
        ->name('register.admin.post');

    Route::post('/register/pegawai', [RegisterController::class, 'registerStaff'])
        ->name('register.staff.post');

    Route::post('/register/jamaah', [RegisterController::class, 'registerJamaah'])
        ->name('register.jamaah.post');
});

Route::post('/verify-code', [AccessCodeController::class, 'verify'])
    ->middleware('throttle:10,1')
    ->name('verify.code');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/jamaah', [JamaahController::class, 'index'])
            ->name('jamaah.index');
            
        Route::resource('pegawai', PegawaiController::class);

        Route::resource('gaji-jabatan', GajiJabatanController::class)
            ->except(['show']);

        Route::resource('coa', CoaController::class)
            ->except(['show']);

        Route::resource('presensi', PresensiController::class)
            ->except(['show']);

        Route::resource('penggajian', PenggajianController::class)
            ->only(['index', 'create', 'store']);

        Route::resource('pengeluaran', PengeluaranController::class)
            ->only(['index', 'create', 'store', 'destroy']);

        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/jurnal-umum', [LaporanController::class, 'jurnalUmum'])
                ->name('jurnal-umum');

            Route::get('/arus-kas', [LaporanController::class, 'arusKas'])
                ->name('arus-kas');

            Route::get('/arus-kas-psak', [LaporanController::class, 'arusKasDariJurnal'])
                ->name('arus-kas-psak');
        });
    });
});

Route::middleware(['auth', 'role:pegawai'])
    ->prefix('pegawai')
    ->name('pegawai.')
    ->group(function () {
        Route::get('/dashboard/{jabatan?}', [PegawaiDashboardController::class, 'index'])
            ->where('jabatan', '[a-z0-9-]+')
            ->name('dashboard');

        Route::get('/presensi', [PresensiController::class, 'pegawaiIndex'])
            ->name('presensi.index');

        Route::get('/presensi/create', [PresensiController::class, 'pegawaiCreate'])
            ->name('presensi.create');

        Route::post('/presensi', [PresensiController::class, 'pegawaiStore'])
            ->name('presensi.store');

        Route::get('/laporan-gaji', [PegawaiDashboardController::class, 'laporanGaji'])
            ->name('laporan-gaji.index');
    });

Route::middleware(['auth', 'verified', 'role:jamaah'])
    ->prefix('jamaah')
    ->name('jamaah.')
    ->group(function () {
        Route::get('/dashboard', [JamaahController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/transaksi/{jenis}', [JamaahController::class, 'createTransaksi'])
            ->whereIn('jenis', ['zakat', 'infak', 'wakaf'])
            ->name('transaksi.create');

        Route::post('/transaksi/{jenis}', [JamaahController::class, 'storeTransaksi'])
            ->whereIn('jenis', ['zakat', 'infak', 'wakaf'])
            ->name('transaksi.store');

        Route::get('/riwayat-transaksi', [JamaahController::class, 'riwayat'])
            ->name('riwayat.index');

        Route::get('/laporan-transaksi', [JamaahController::class, 'laporan'])
            ->name('laporan.index');

        Route::get('/laporan-transaksi/export', [JamaahController::class, 'exportLaporan'])
            ->name('laporan.export');
    });

Route::post('/session/heartbeat', fn () => response()->noContent())
    ->middleware('auth')
    ->name('session.heartbeat');

require __DIR__.'/auth.php';