<?php

namespace App\Services\Auth;

use App\Models\OtpVerification;
use App\Services\Sms\FourJawalyService;

class OtpService
{
    /**
     * Temporary fixed OTP code used until random codes are enabled.
     */
    public const FIXED_OTP_CODE = '1234';

    public function __construct(public FourJawalyService $smsService) {}

    /**
     * Generate, deliver by SMS and store a new OTP code.
     *
     * Returns the generated code, or null when the SMS could not be delivered.
     */
    public function issue(string $phone, string $countryCode, string $purpose, string $locale = 'ar', int $expiresInMinutes = 5): ?string
    {
        $otpCode = self::FIXED_OTP_CODE;

        if ($this->smsService->isEnabled()) {
            $isSent = $this->smsService->send($phone, $this->buildMessage($otpCode, $locale), $countryCode);

            if (! $isSent) {
                return null;
            }
        }

        OtpVerification::where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        OtpVerification::create([
            'phone_country_code' => $countryCode,
            'phone' => $phone,
            'otp_code' => $otpCode,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes($expiresInMinutes),
            'is_used' => false,
        ]);

        return $otpCode;
    }

    /**
     * Build the localized SMS text for the OTP code.
     */
    protected function buildMessage(string $otpCode, string $locale): string
    {
        return $locale === 'en'
            ? "Your Taba3a verification code is: {$otpCode}"
            : "رمز التحقق الخاص بك في طباعة هو: {$otpCode}";
    }
}
