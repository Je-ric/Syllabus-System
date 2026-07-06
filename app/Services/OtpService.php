<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public const PURPOSE_PASSWORD_CHANGE = 'password_change';

    private const DEFAULT_EXPIRY_MINUTES = 10;

    public function issueForUser(
        User $user,
        string $purpose,
        int $expiresInMinutes = self::DEFAULT_EXPIRY_MINUTES
    ): bool
    {
        $otp = (string) random_int(100000, 999999);

        UserOtp::updateOrCreate(
            [
                'user_id' => $user->id,
                'purpose' => $purpose,
            ],
            [
                'otp' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes($expiresInMinutes),
            ]
        );

        try {
            Mail::to($user->email)->send(new OtpMail($otp));
            return true;
        } catch (\Exception $e) {
            Log::error('OTP mail failed for user ' . $user->id . ': ' . $e->getMessage());
            return false;
        }
    }

    public function validate(User $user, string $otp, string $purpose): ?string
    {
        $otpRecord = UserOtp::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->first();

        if (!$otpRecord) {
            return 'OTP is invalid or has already been used.';
        }

        if ($otpRecord->otp_expires_at && now()->greaterThan($otpRecord->otp_expires_at)) {
            return 'OTP has expired. Please request a new one.';
        }

        if (!Hash::check($otp, $otpRecord->otp)) {
            return 'Invalid OTP.';
        }

        return null;
    }

    public function clear(User $user, string $purpose): void
    {
        UserOtp::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->delete();
    }

}
