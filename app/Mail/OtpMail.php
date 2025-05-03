<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use SerializesModels;

    public $otp;

    // Kirim OTP ke constructor
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Your OTP Code')
            ->html('<p>Your OTP code is: <strong>' . $this->otp . '</strong></p><p>This code will expire in 10 minutes.</p>');
    }
}
