<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\OtpMail;

class OtpService
{
    /**
     * Generate a 6-digit OTP
     */
    public function generateOtp(): string
    {
        return sprintf('%06d', mt_rand(0, 999999));
    }

    /**
     * Store OTP in cache with expiration
     */
    public function storeOtp(string $email, string $otp, int $expirationMinutes = 10): void
    {
        $key = $this->getCacheKey($email);
        Cache::put($key, [
            'otp' => $otp,
            'attempts' => 0,
            'created_at' => now()
        ], now()->addMinutes($expirationMinutes));
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(string $email, string $otp): bool
    {
        $key = $this->getCacheKey($email);
        $data = Cache::get($key);

        if (!$data) {
            return false; // OTP not found or expired
        }

        // Check attempt limit (max 3 attempts)
        if ($data['attempts'] >= 3) {
            Cache::forget($key);
            return false;
        }

        // Increment attempts
        $data['attempts']++;
        Cache::put($key, $data, now()->addMinutes(10));

        if ($data['otp'] === $otp) {
            // OTP is correct, remove from cache
            Cache::forget($key);
            return true;
        }

        return false;
    }

    /**
     * Send OTP via email
     */
    public function sendOtp(string $email, string $type = 'user'): string
    {
        $otp = $this->generateOtp();
        $this->storeOtp($email, $otp);

        try {
            Mail::to($email)->send(new OtpMail($otp, $type));
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
            // For demo purposes, we'll still return the OTP even if email fails
        }

        return $otp;
    }

    /**
     * Get cache key for email
     */
    private function getCacheKey(string $email): string
    {
        return 'otp_' . md5($email);
    }

    /**
     * Clear OTP for email
     */
    public function clearOtp(string $email): void
    {
        $key = $this->getCacheKey($email);
        Cache::forget($key);
    }

    /**
     * Check if OTP exists for email
     */
    public function hasOtp(string $email): bool
    {
        $key = $this->getCacheKey($email);
        return Cache::has($key);
    }

    /**
     * Get remaining attempts for email
     */
    public function getRemainingAttempts(string $email): int
    {
        $key = $this->getCacheKey($email);
        $data = Cache::get($key);

        if (!$data) {
            return 0;
        }

        return max(0, 3 - $data['attempts']);
    }
}
