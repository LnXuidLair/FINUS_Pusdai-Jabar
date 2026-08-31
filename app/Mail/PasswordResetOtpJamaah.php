<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpJamaah extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $name,
        public int $expiresInSeconds = 20
    ) {
    }

    public function build(): self
    {
        return $this->subject('Kode OTP Reset Password FINUS')
            ->view('emails.password-reset-otp-jamaah');
    }
}
