<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function adminLogin(Request $request)
    {
        return $this->login($request, 'admin', 'dashboard', 'admin');
    }

    public function staffLogin(Request $request)
    {
        return $this->login($request, 'pegawai', 'pegawai.dashboard', 'pegawai');
    }

    public function jamaahLogin(Request $request)
    {
        return $this->login($request, 'jamaah', 'jamaah.dashboard', 'jamaah');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.jamaah');
    }

    private function login(Request $request, string $role, string $route, string $label)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $emailRules = ['required', 'email'];

        if ($role === 'jamaah') {
            $emailRules[] = 'regex:/^[A-Z0-9._%+\-]+@gmail\.com$/i';
        }

        $credentials = $request->validate([
            'email' => $emailRules,
            'password' => ['required', 'string'],
        ], [
            'email.regex' => 'Akun jamaah wajib menggunakan alamat Gmail (@gmail.com).',
        ]);

        if (!Auth::attempt($credentials + ['role' => $role], $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => "Email atau password {$label} salah."])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('last_activity_at', now()->timestamp);

        if ($role === 'jamaah' && !$request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended(route($route));
    }
}
