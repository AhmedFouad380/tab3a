<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlRajhiPaymentService
{
    /**
     * Standard fixed IV used by Al Rajhi Bank REST API.
     */
    public const DEFAULT_IV = 'PGKEYENCDECIVSPC';

    protected string $baseUrl;

    protected ?string $tranportalId;

    protected ?string $password;

    protected ?string $resourceKey;

    protected string $currencyCode;

    protected string $mode;

    public function __construct()
    {
        $this->loadConfiguration();
    }

    /**
     * Load settings from config/services.php or system_settings fallback.
     */
    public function loadConfiguration(): void
    {
        $this->baseUrl = (string) (config('services.alrajhi.base_url')
            ?: SystemSetting::getSetting('alrajhi_base_url', 'ar', 'https://securepayments.alrajhibank.com.sa/pg/payment/rest/'));

        // Ensure trailing slash
        $this->baseUrl = rtrim($this->baseUrl, '/').'/';

        $this->tranportalId = config('services.alrajhi.tranportal_id')
            ?: SystemSetting::getSetting('alrajhi_tranportal_id', 'ar');

        $this->password = config('services.alrajhi.password')
            ?: SystemSetting::getSetting('alrajhi_password', 'ar');

        $this->resourceKey = config('services.alrajhi.resource_key')
            ?: SystemSetting::getSetting('alrajhi_resource_key', 'ar');

        $this->currencyCode = (string) (config('services.alrajhi.currency_code')
            ?: SystemSetting::getSetting('alrajhi_currency_code', 'ar', '682'));

        $this->mode = (string) (config('services.alrajhi.mode')
            ?: SystemSetting::getSetting('alrajhi_mode', 'ar', 'sandbox'));
    }

    /**
     * Check if Al Rajhi gateway credentials are fully configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->tranportalId)
            && ! empty($this->password)
            && ! empty($this->resourceKey);
    }

    /**
     * Format resource key to 16-byte binary key suitable for AES-128.
     */
    public function formatKey(?string $key = null): string
    {
        $key = $key ?: (string) $this->resourceKey;

        if (strlen($key) === 32 && ctype_xdigit($key)) {
            $binary = hex2bin($key);
            if ($binary !== false) {
                return $binary;
            }
        }

        if (strlen($key) === 16) {
            return $key;
        }

        return substr(hash('sha256', $key, true), 0, 16);
    }

    /**
     * Encrypt an associative array payload into Al Rajhi uppercase hex string.
     */
    public function encrypt(array $data, ?string $key = null): string
    {
        $plainText = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $binaryKey = $this->formatKey($key);

        $encrypted = openssl_encrypt(
            $plainText,
            'aes-128-cbc',
            $binaryKey,
            OPENSSL_RAW_DATA,
            self::DEFAULT_IV
        );

        if ($encrypted === false) {
            throw new Exception('Al Rajhi Encryption Failed: '.openssl_error_string());
        }

        return strtoupper(bin2hex($encrypted));
    }

    /**
     * Decrypt Al Rajhi hex response string back to an associative array.
     */
    public function decrypt(string $encryptedHex, ?string $key = null): array
    {
        $binaryKey = $this->formatKey($key);
        $cleanHex = trim($encryptedHex);

        $ciphertext = @hex2bin($cleanHex);
        if ($ciphertext === false) {
            throw new Exception('Al Rajhi Decryption Failed: Invalid hex string provided.');
        }

        $decrypted = openssl_decrypt(
            $ciphertext,
            'aes-128-cbc',
            $binaryKey,
            OPENSSL_RAW_DATA,
            self::DEFAULT_IV
        );

        if ($decrypted === false) {
            throw new Exception('Al Rajhi Decryption Failed: '.openssl_error_string());
        }

        $result = json_decode($decrypted, true);
        if (! is_array($result)) {
            throw new Exception('Al Rajhi Decryption Failed: Decrypted text is not valid JSON.');
        }

        return $result;
    }

    /**
     * Initiate a payment session with Al Rajhi Bank for an Order.
     *
     * @return array{success: bool, payment_url: ?string, payment_id: ?string, raw: array, error: ?string}
     */
    public function initiatePayment(Order $order, string $responseUrl, string $errorUrl, array $extra = []): array
    {
        if (! $this->isConfigured()) {
            throw new Exception('بيانات اعتماد بوابة دفع الراجحي غير مكتملة (Tranportal ID, Password, Resource Key).');
        }

        $trackId = $order->order_number;
        $amount = number_format((float) $order->total_amount, 2, '.', '');

        $payload = array_merge([
            'id' => $this->tranportalId,
            'password' => $this->password,
            'action' => '1', // 1 indicates purchase
            'amt' => $amount,
            'currencyCode' => $this->currencyCode,
            'trackId' => $trackId,
            'responseURL' => $responseUrl,
            'errorURL' => $errorUrl,
            'udf1' => (string) $order->id,
            'udf2' => (string) $order->user_id,
            'udf3' => (string) $order->order_type,
        ], $extra);

        $encryptedTrandata = $this->encrypt($payload);

        $requestBody = [
            'id' => $this->tranportalId,
            'trandata' => $encryptedTrandata,
            'operat' => 'webView',
        ];

        Log::info('Al Rajhi Payment Initiation Request', [
            'order_number' => $order->order_number,
            'amount' => $amount,
            'mode' => $this->mode,
        ]);

        try {
            $clientIp = request()->ip() ?? '127.0.0.1';
            $response = Http::timeout(25)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-FORWARDED-FOR' => $clientIp,
                ])
                ->post($this->baseUrl.'performTransaction', $requestBody);

            if (! $response->successful()) {
                Log::error('Al Rajhi Gateway HTTP Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'payment_url' => null,
                    'payment_id' => null,
                    'raw' => [],
                    'error' => 'تعذر الاتصال بخادم بنك الراجحي: كود '.$response->status(),
                ];
            }

            $responseJson = $response->json();
            $encryptedResponse = $responseJson['trandata'] ?? null;

            if (empty($encryptedResponse)) {
                return [
                    'success' => false,
                    'payment_url' => null,
                    'payment_id' => null,
                    'raw' => $responseJson ?? [],
                    'error' => $responseJson['error'] ?? 'لم يتم استلام نص التشفير trandata من البنك.',
                ];
            }

            $decrypted = $this->decrypt($encryptedResponse);

            Log::info('Al Rajhi Payment Initiation Response Decrypted', [
                'order_number' => $order->order_number,
                'result' => $decrypted['result'] ?? null,
                'payment_id' => $decrypted['paymentId'] ?? $decrypted['paymentid'] ?? null,
            ]);

            $paymentUrl = $decrypted['paymentURL']
                ?? $decrypted['paymentUrl']
                ?? $decrypted['targetUrl']
                ?? null;

            $paymentId = $decrypted['paymentId']
                ?? $decrypted['paymentid']
                ?? $decrypted['tranid']
                ?? null;

            if (empty($paymentUrl)) {
                return [
                    'success' => false,
                    'payment_url' => null,
                    'payment_id' => $paymentId,
                    'raw' => $decrypted,
                    'error' => $decrypted['error_text'] ?? $decrypted['result'] ?? 'فشل إنشاء رابط الدفع لدى البنك.',
                ];
            }

            return [
                'success' => true,
                'payment_url' => $paymentUrl,
                'payment_id' => $paymentId,
                'raw' => $decrypted,
                'error' => null,
            ];

        } catch (Exception $e) {
            Log::error('Al Rajhi Initiate Payment Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'payment_url' => null,
                'payment_id' => null,
                'raw' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify and parse the incoming callback trandata from Al Rajhi.
     *
     * @return array{is_paid: bool, result_code: string, transaction_id: ?string, track_id: ?string, amount: ?string, raw: array, error: ?string}
     */
    public function verifyCallback(string $encryptedTrandata): array
    {
        try {
            $data = $this->decrypt($encryptedTrandata);

            // In Al Rajhi specs, successful payment returns result 'CAPTURED'
            $resultCode = strtoupper((string) ($data['result'] ?? ''));
            $isPaid = ($resultCode === 'CAPTURED' || $resultCode === 'SUCCESS' || $resultCode === 'APPROVED');

            return [
                'is_paid' => $isPaid,
                'result_code' => $resultCode,
                'transaction_id' => $data['tranid'] ?? $data['paymentId'] ?? null,
                'track_id' => $data['trackId'] ?? $data['trackid'] ?? null,
                'amount' => $data['amt'] ?? null,
                'auth_code' => $data['authCode'] ?? null,
                'raw' => $data,
                'error' => $isPaid ? null : ($data['error_text'] ?? "حالة العملية: {$resultCode}"),
            ];

        } catch (Exception $e) {
            Log::error('Al Rajhi Verify Callback Error', [
                'message' => $e->getMessage(),
            ]);

            return [
                'is_paid' => false,
                'result_code' => 'DECRYPTION_ERROR',
                'transaction_id' => null,
                'track_id' => null,
                'amount' => null,
                'raw' => [],
                'error' => 'فشل فك تشفير استجابة البنك: '.$e->getMessage(),
            ];
        }
    }
}
