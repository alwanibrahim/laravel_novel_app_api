<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',   // Tambahkan kolom email ke fillable
        'otp',     // Tambahkan kolom otp ke fillable
        'expires_at', // Tambahkan kolom expires_at ke fillable
        'sent_at',
        'verified_at',
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Menandai bahwa verifikasi email sudah berhasil
    public function markAsVerified()
    {
        $this->verified_at = now();
        $this->save();
    }

    // Menandai bahwa verifikasi email sudah dikirim
    public function markAsSent()
    {
        $this->sent_at = now();
        $this->save();
    }
}
