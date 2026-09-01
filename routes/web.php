<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AgendaKegiatanController;
use App\Http\Controllers\AccessCodeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\StaffActivationController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GajiJabatanController;
use App\Http\Controllers\JamaahController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PegawaiDashboardController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ZiswafTransactionController;
use App\Http\Middleware\EnsureManagementAccess;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/visi-misi', 'visi-misi')->name('visi-misi');
Route::view('/location', 'location')->name('location');
Route::view('/management-access', 'pengelola')
    ->name('management.access');
Route::get('/login', fn () => redirect()->route('home'))
    ->name('login');
Route::get('/register', fn () => redirect()->route('login.jamaah'));
Route::post('/verify-code', [AccessCodeController::class, 'verify'])
    ->middleware('throttle:10,1')
    ->name('verify.code');
Route::middleware(['guest:admin', EnsureManagementAccess::class . ':admin'])
    ->group(function(){
        Route::get('/login/admin', function () {
            return User::where('role', User::ROLE_ADMIN)
            ->exists() ? view('auth.login-admin') : redirect()->route('register.admin');})
            ->name('login.admin');
        Route::post('/login/admin', [LoginController::class, 'adminLogin']);
        Route::get('/register/admin', function () {
            if(User::where('role', User::ROLE_ADMIN)
                ->exists()
            ){
                return redirect()
                ->route('login.admin')
                ->withErrors(['name' => 'Admin sudah tersedia.',]);
            }
            return view('auth.register-admin');})
            ->name('register.admin');
        Route::post('/register/admin', [RegisterController::class, 'registerAdmin'])
            ->name('register.admin.post');
        Route::post('/register/admin/recovery-code/generate', [RegisterController::class, 'generateAdminRecoveryCode'])
            ->middleware('throttle:20,1')
            ->name('register.admin.recovery-code.generate');
    });
Route::middleware(['guest:pegawai', EnsureManagementAccess::class . ':staff',])
    ->group(function(){
        Route::view('/login/pegawai', 'auth.login-staff')
            ->name('login.staff');
        Route::post('/login/pegawai', [LoginController::class, 'staffLogin']);
        Route::get('/register/pegawai', [StaffActivationController::class, 'create'])
            ->name('register.staff');
        Route::post('/verifikasi/pegawai', [StaffActivationController::class, 'verify'])
            ->middleware('throttle:10,1')
            ->name('verify.staff');
        Route::get('/register/pegawai/akun', [StaffActivationController::class, 'createPassword'])
            ->name('register.staff.account');
        Route::post('/register/pegawai', [StaffActivationController::class, 'storePassword'])
            ->name('register.staff.post');
        Route::get('/register/pegawai/selesai', [StaffActivationController::class, 'success'])
            ->name('register.staff.success');
    });
Route::middleware('guest:jamaah')
    ->group(function(){
        Route::view('/login/jamaah', 'auth.login-jamaah')
            ->name('login.jamaah');
        Route::post('/login/jamaah', [LoginController::class, 'jamaahLogin']);
        Route::view('/register/jamaah', 'auth.register-jamaah')
            ->name('register.jamaah');
        Route::post('/register/jamaah', [RegisterController::class, 'registerJamaah'])
            ->name('register.jamaah.post');
    });
Route::middleware(['auth:admin', 'role:admin'])
    ->group(function(){
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/admin/profil', [AccountController::class, 'adminProfile'])
            ->name('admin.profile');
        Route::patch('/admin/profil', [AccountController::class, 'updateAdminProfile'])
            ->name('admin.profile.update');
        Route::get('/admin/pengaturan', [AccountController::class, 'adminSettings'])
            ->name('admin.settings');
        Route::post('/admin/pengaturan/recovery-code/generate', [AccountController::class, 'generateAdminRecoveryCode'])
            ->middleware('throttle:20,1')
            ->name('admin.recovery-code.generate');
        Route::patch('/admin/pengaturan/recovery-code', [AccountController::class, 'updateAdminRecoveryCode'])
            ->middleware('throttle:10,1')
            ->name('admin.recovery-code.update');
        Route::get('/admin/ubah-password', function () {
            return view('auth.change-password', [
                'portal' => 'admin',
                'updateRoute' => 'admin.password.update',
                'backRoute' => 'admin.settings',
            ]);
        })->name('admin.password.edit');
        Route::patch('/admin/ubah-password', [PasswordController::class, 'update'])
            ->defaults('auth_guard', User::ROLE_ADMIN)
            ->name('admin.password.update');
        Route::prefix('admin')
            ->name('admin.')
            ->group(function(){
                Route::get('/jamaah', [JamaahController::class, 'index'])
                    ->name('jamaah.index');
                Route::prefix('ziswaf')
                    ->name('ziswaf.')
                    ->group(function () {
                        Route::get('/transaksi', [ZiswafTransactionController::class, 'index'])
                            ->name('transaksi.index');
                        Route::patch('/transaksi/{transaksi}/terima', [ZiswafTransactionController::class, 'terima'])
                            ->name('transaksi.terima');
                        Route::patch('/transaksi/{transaksi}/tolak', [ZiswafTransactionController::class, 'tolak'])
                            ->name('transaksi.tolak');
                    });
                Route::resource('pegawai', PegawaiController::class);
                Route::resource('gaji-jabatan', GajiJabatanController::class)
                    ->except(['show']);
                Route::get('coa/template', [CoaController::class, 'downloadTemplate'])
                    ->name('coa.template');
                Route::post('coa/import', [CoaController::class, 'import'])
                    ->name('coa.import');
                Route::resource('coa', CoaController::class)
                    ->except(['show']);
                Route::patch('presensi/approve-bulk', [PresensiController::class, 'approveBulk'])
                    ->name('presensi.approve-bulk');
                Route::patch('presensi/{presensi}/approve', [PresensiController::class, 'approve'])
                    ->name('presensi.approve');
                Route::resource('presensi', PresensiController::class)
                    ->except(['show']);
                Route::get('penggajian', [PenggajianController::class, 'index'])
                    ->name('penggajian.index');
                Route::patch('penggajian/{penggajian}/status', [PenggajianController::class, 'updateStatus'])
                    ->name('penggajian.status');
                Route::resource('pengeluaran', PengeluaranController::class)
                    ->only(['index', 'create', 'store', 'destroy',]);
                Route::prefix('laporan')
                    ->name('laporan.')
                    ->group(function (){
                        Route::get('/jurnal-umum', [LaporanController::class, 'jurnalUmum'])
                            ->name('jurnal-umum');
                        Route::get('/arus-kas', [LaporanController::class, 'arusKas'])
                            ->name('arus-kas');
                    });
                Route::resource('agenda-kegiatan', AgendaKegiatanController::class)
                    ->except(['show']);
                Route::patch('agenda-kegiatan/{agendaKegiatan}/toggle', [AgendaKegiatanController::class, 'toggleAktif'])
                    ->name('agenda-kegiatan.toggle');
                Route::get('pemasukan', [PemasukanController::class, 'index'])
                    ->name('pemasukan.index');
                Route::post('pemasukan', [PemasukanController::class, 'store'])
                    ->name('pemasukan.store');
                Route::patch('pemasukan/{pemasukan}/verifikasi', [PemasukanController::class, 'verifikasi'])
                    ->name('pemasukan.verifikasi');
                Route::delete('pemasukan/{pemasukan}', [PemasukanController::class, 'destroy'])
                    ->name('pemasukan.destroy');
            });
    });
Route::middleware(['auth:pegawai', 'role:pegawai',])
    ->prefix('pegawai')
    ->name('pegawai.')
    ->group(function(){
        Route::get('/profil', [AccountController::class, 'pegawaiProfile'])
            ->name('profile');
        Route::get('/pengaturan', [AccountController::class, 'pegawaiSettings'])
            ->name('settings');
        Route::get('/ubah-password', function () {
            return view('auth.change-password', [
                'portal' => 'staff',
                'updateRoute' => 'pegawai.password.update',
                'backRoute' => 'pegawai.settings',
            ]);
        })->name('password.edit');
        Route::patch('/ubah-password', [PasswordController::class, 'update'])
            ->defaults('auth_guard', User::ROLE_PEGAWAI)
            ->name('password.update');
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
        Route::get('/laporan-gaji/{penggajian}/slip', [PegawaiDashboardController::class, 'downloadSlip'])
            ->name('laporan-gaji.slip');
        Route::middleware('pegawai.access:dkm,keuangan')
            ->prefix('laporan-keuangan')
            ->name('laporan-keuangan.')
            ->group(function () {
                Route::get('/jurnal-umum', [LaporanController::class, 'jurnalUmum'])
                    ->name('jurnal-umum');
                Route::get('/arus-kas', [LaporanController::class, 'arusKas'])
                    ->name('arus-kas');
            });
        Route::middleware('pegawai.access:keuangan')
            ->prefix('keuangan')
            ->name('keuangan.')
            ->group(function () {
                Route::get('pemasukan', [PemasukanController::class, 'index'])
                    ->name('pemasukan.index');
                Route::post('pemasukan', [PemasukanController::class, 'store'])
                    ->name('pemasukan.store');
                Route::patch('pemasukan/{pemasukan}/verifikasi', [PemasukanController::class, 'verifikasi'])
                    ->name('pemasukan.verifikasi');
                Route::delete('pemasukan/{pemasukan}', [PemasukanController::class, 'destroy'])
                    ->name('pemasukan.destroy');

                Route::resource('pengeluaran', PengeluaranController::class)
                    ->only(['index', 'create', 'store', 'destroy']);
                Route::get('penggajian', [PenggajianController::class, 'index'])
                    ->name('penggajian.index');
                Route::patch('penggajian/{penggajian}/status', [PenggajianController::class, 'updateStatus'])
                    ->name('penggajian.status');
            });
    });
Route::middleware(['auth:jamaah', 'verified', 'role:jamaah',])
    ->prefix('jamaah')
    ->name('jamaah.')
    ->group(function (){
        Route::get('/profil', [AccountController::class, 'jamaahProfile'])
            ->name('profile');
        Route::get('/pengaturan', [AccountController::class, 'jamaahSettings'])
            ->name('settings');
        Route::get('/ubah-password', function () {
            return view('auth.change-password', [
                'portal' => 'jamaah',
                'updateRoute' => 'jamaah.password.update',
                'backRoute' => 'jamaah.settings',
            ]);
        })->name('password.edit');
        Route::patch('/ubah-password', [PasswordController::class, 'update'])
            ->defaults('auth_guard', User::ROLE_JAMAAH)
            ->name('password.update');
        Route::get('/dashboard', [JamaahController::class, 'dashboard'])
            ->name('dashboard');
        Route::get('/transaksi/{jenis}', [JamaahController::class, 'createTransaksi'])
            ->whereIn('jenis', ['zakat', 'infak', 'wakaf'])
            ->name('transaksi.create');
        Route::post('/transaksi/{jenis}', [JamaahController::class, 'storeTransaksi'])
            ->whereIn('jenis', ['zakat', 'infak', 'wakaf'])
            ->name('transaksi.store');
        Route::get('/pembayaran/{transaksi}', [JamaahController::class, 'showPembayaran'])
            ->name('pembayaran.show');
        Route::delete('/pembayaran/{transaksi}/batal', [JamaahController::class, 'batalPembayaran'])
            ->name('pembayaran.batal');
        Route::get('/pembayaran/{transaksi}/cek-status', [JamaahController::class, 'cekStatusPembayaran'])
            ->name('pembayaran.cek-status');
        Route::get('/pembayaran/{transaksi}/poll-status', [JamaahController::class, 'pollStatusPembayaran'])   
            ->name('pembayaran.poll-status');
        Route::get('/riwayat-transaksi', [JamaahController::class, 'riwayat'])
            ->name('riwayat.index');
        Route::get('/laporan-transaksi', [JamaahController::class, 'laporan'])
            ->name('laporan.index');
        Route::get('/laporan-transaksi/export', [JamaahController::class, 'exportLaporan'])
            ->name('laporan.export');});
Route::post('/payment/midtrans/notification', [JamaahController::class, 'midtransNotification'])
    ->name('payment.midtrans.notification');
Route::post('/session/heartbeat/admin', fn () => response()->noContent())
    ->middleware('auth:admin')
    ->name('session.heartbeat.admin');
Route::post('/session/heartbeat/pegawai', fn () => response()->noContent())
    ->middleware('auth:pegawai')
    ->name('session.heartbeat.pegawai');
Route::post('/session/heartbeat/jamaah', fn () => response()->noContent())
    ->middleware('auth:jamaah')
    ->name('session.heartbeat.jamaah');
Route::post('/session/heartbeat', function(){$hasActiveSession = collect(['admin', 'pegawai', 'jamaah', 'web',])
    ->contains(fn (string $guard): bool =>Auth::guard($guard)->check());
    abort_unless($hasActiveSession, 401);
    return response()->noContent();
})->name('session.heartbeat');
require __DIR__ . '/auth.php';