<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'purpose',
        'otp',
        'otp_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'otp_expires_at' => 'datetime',
        ];
    }

    // Used in: issueForUser() - Authentication\OtpService;
    //          validate() - Authentication\OtpService;
    //          clear() - Authentication\OtpService;
    //          migrateLegacyOtp() - Authentication\OtpService
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
