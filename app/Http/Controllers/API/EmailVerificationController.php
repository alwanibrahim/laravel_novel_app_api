<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailVerification;
use App\Models\OtpCode;
use App\Mail\OtpMail;
use App\Models\User;//aman

class EmailVerificationController extends Controller
{
    // Fungsi untuk mengirim OTP ke email
    public function sendOtp(Request $request)
    {
        $user = $request->user();

        $configValues = [
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => substr(config('mail.mailers.smtp.password'), 0, 3) . '...'
        ];

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email sudah diverifikasi']);
        }

        // Cek kalau baru saja kirim OTP
        $verif = EmailVerification::where('email', $user->email)->first();
        if ($verif && $verif->updated_at->diffInSeconds(now()) < 60) {
            return response()->json([
                'message' => 'Tunggu sebentar sebelum meminta OTP lagi.',
            ], 429);
        }

        $otp = rand(100000, 999999);

        EmailVerification::updateOrCreate(
            ['email' => $user->email],
            ['otp' => $otp, 'expires_at' => now()->addMinutes(10)]
        );

        try {
            Mail::to($user->email)->send(new OtpMail($otp));

            return response()->json([
                'message' => 'Kode OTP berhasil dikirim',
                'debug_info' => $configValues
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengirim OTP: ' . $e->getMessage(),
                'debug_info' => $configValues
            ], 500);
        }
    }


    public function verifyOtp(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan atau belum login'], 400);
        }

        // Tambahkan untuk debugging
        $configValues = [
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => substr(config('mail.mailers.smtp.password'), 0, 3) . '...'
        ];

        // Lanjutkan proses verifikasi OTP
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        // Cek OTP - pastikan model yang digunakan konsisten
        $otp = $request->input('otp');

        // Jika menggunakan EmailVerification model (sesuai dengan sendOtpVerification)
        $verification = EmailVerification::where('email', $user->email)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->first();


        if (!$verification) {
            return response()->json(['message' => 'OTP salah atau kadaluarsa'], 400);
        }

        // Update user dan set email_verified_at dan is_verified
        $user->email_verified_at = now();
        $user->is_verified = true;  // Kolom ini harus ada di tabel users
        $user->save();

        $user->refresh();
        $verification->delete();

        return response()->json([
            'message' => 'Email berhasil diverifikasi',
            'user' => $user
        ]);
    }

    // Fungsi untuk mengirim OTP secara manual
    public function sendOtpManually(User $user)
    {

        $configValues = [
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => substr(config('mail.mailers.smtp.password'), 0, 3) . '...'
        ];

        // Generate OTP
        $otp = rand(100000, 999999);

        // Simpan OTP ke database (tabel otp_codes)
        OtpCode::updateOrCreate(
            ['user_id' => $user->id],
            ['code' => $otp, 'expired_at' => now()->addMinutes(10)]
        );

        // Kirim OTP ke email menggunakan Mailable
        try {
            Mail::to($user->email)->send(new OtpMail($otp));
            return response()->json(['message' => 'Kode OTP dikirim ke email']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengirim OTP: ' . $e->getMessage()], 500);
        }
    }
}
