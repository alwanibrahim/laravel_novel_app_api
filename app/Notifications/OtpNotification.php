<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OtpNotification extends Notification
{
    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Kode OTP Kamu')
            ->greeting('Halo!')
            ->line("Kode OTP kamu adalah: **{$this->otp}**")
            ->line('Kode ini berlaku selama 5 menit.')
            ->salutation('Terima kasih.');
    }
}
