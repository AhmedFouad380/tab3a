<?php

namespace App\Services\Sms;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FourJawalyService
{
    /**
     * Determine whether real SMS sending is enabled and configured.
     */
    public function isEnabled(): bool
    {
        return (bool) config('services.fourjawaly.enabled')
            && filled(config('services.fourjawaly.api_key'))
            && filled(config('services.fourjawaly.api_secret'))
            && filled(config('services.fourjawaly.sender'));
    }

    /**
     * Send an SMS message to a single phone number.
     */
    public function send(string $phone, string $message, string $countryCode = '+966'): bool
    {
        $number = $this->formatNumber($phone, $countryCode);

        try {
            $response = Http::baseUrl(rtrim((string) config('services.fourjawaly.base_url'), '/'))
                ->withBasicAuth((string) config('services.fourjawaly.api_key'), (string) config('services.fourjawaly.api_secret'))
                ->acceptJson()
                ->timeout(15)
                ->post('/account/area/sms/send', [
                    'messages' => [
                        [
                            'text' => $message,
                            'numbers' => [$number],
                            'sender' => (string) config('services.fourjawaly.sender'),
                        ],
                    ],
                ]);
        } catch (ConnectionException $exception) {
            Log::error('4jawaly SMS connection failed', [
                'number' => $number,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }

        $isSent = $response->successful() && (int) $response->json('code', $response->status()) === 200;

        if (! $isSent) {
            Log::error('4jawaly SMS sending failed', [
                'number' => $number,
                'status' => $response->status(),
                'response' => $response->json() ?? $response->body(),
            ]);
        }

        return $isSent;
    }

    /**
     * Convert a local or international phone number to the 4jawaly format (e.g. 9665XXXXXXXX).
     */
    public function formatNumber(string $phone, string $countryCode = '+966'): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        $countryDigits = preg_replace('/\D/', '', $countryCode) ?: '966';

        if (str_starts_with($digits, '00')) {
            return substr($digits, 2);
        }

        if (str_starts_with($digits, $countryDigits)) {
            return $digits;
        }

        return $countryDigits.ltrim($digits, '0');
    }
}
