<?php

use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Pemulihan Password FINUS
|--------------------------------------------------------------------------
| Controller lama tetap digunakan dan disesuaikan:
| - PasswordResetLinkController: tahap awal recovery + OTP Jamaah.
| - NewPasswordController: simpan password baru setelah recovery lolos.
*/
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('password.email');

Route::get('/forgot-password/otp', [PasswordResetLinkController::class, 'showOtp'])
    ->name('password.otp.form');

Route::post('/forgot-password/otp', [PasswordResetLinkController::class, 'verifyOtp'])
    ->middleware('throttle:5,1')
    ->name('password.otp.verify');

Route::post('/forgot-password/otp/resend', [PasswordResetLinkController::class, 'resendOtp'])
    ->middleware('throttle:3,1')
    ->name('password.otp.resend');

Route::get('/reset-password', [NewPasswordController::class, 'create'])
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| Verifikasi Email Jamaah dengan OTP 6 Digit
|--------------------------------------------------------------------------
| Fitur lama tetap menggunakan controller verifikasi yang sudah ada,
| tetapi logikanya disesuaikan dari link verifikasi menjadi kode OTP.
*/
Route::middleware('auth:jamaah')->group(function () {
    Route::get('/verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::post('/verify-email/code', VerifyEmailController::class)
        ->middleware('throttle:5,1')
        ->name('verification.code.verify');

    Route::post(
        '/email/verification-notification',
        [EmailVerificationNotificationController::class, 'store']
    )
        ->middleware('throttle:3,1')
        ->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Logout per guard
|--------------------------------------------------------------------------
*/
Route::post('/logout/admin', [LoginController::class, 'adminLogout'])
    ->middleware('auth:admin')
    ->name('logout.admin');

Route::post('/logout/pegawai', [LoginController::class, 'staffLogout'])
    ->middleware('auth:pegawai')
    ->name('logout.pegawai');

Route::post('/logout/jamaah', [LoginController::class, 'jamaahLogout'])
    ->middleware('auth:jamaah')
    ->name('logout.jamaah');

Route::post('/logout', [LoginController::class, 'logoutLegacy'])
    ->name('logout');