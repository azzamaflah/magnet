<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // Pastikan Anda mengimpor Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite; // Impor Socialite
use Exception;

class SocialiteController extends Controller
{
    /**
     * Arahkan pengguna ke halaman autentikasi Google.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Dapatkan informasi pengguna dari Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cari user berdasarkan email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // --- USER SUDAH ADA ---

                // Jika user adalah admin, tolak login Google
                if ($user->role === 'admin') {
                    return redirect()->route('login')->withErrors(['email' => 'Akun admin harus login dengan email dan password.']);
                }
                
                // Jika user biasa, loginkan
                Auth::login($user);

            } else {
                // --- USER BELUM ADA, BUAT BARU ---
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'role' => 'user', // PENTING: Set role sebagai 'user'
                    'email_verified_at' => now(), // Email Google sudah terverifikasi
                    'password' => Hash::make(Str::random(24)) // Buat password acak
                ]);

                Auth::login($newUser);
            }

            // Arahkan SEMUA user yang berhasil login ke dashboard
            return redirect()->route('dashboard');

        } catch (Exception $e) {
            // Tangani jika ada error
            report($e);
            return redirect()->route('login')->withErrors(['email' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }
    }
}