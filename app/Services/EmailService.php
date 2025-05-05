<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmailService
{
    public static function sendOTP($toEmail, $subject, $otpCode)
    {
        $apiKey = env('RESEND_API_KEY');

        $response = Http::withToken($apiKey)
            ->post('https://api.resend.com/emails', [
                'from' => 'Wanoman <onboarding@resend.dev>',
                'to' => $toEmail,
                'subject' => $subject,
                'html' => "<p>Halo <strong>$toEmail</strong>, ini kode OTP kamu:</p><h2>$otpCode</h2><p>Kode ini berlaku 10 menit.</p>"
            ]);

        return $response->json();
    }
}
