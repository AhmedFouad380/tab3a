<?php

namespace Tests\Feature;

use App\Models\OtpVerification;
use App\Models\User;
use App\Services\Sms\FourJawalyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FourJawalyOtpTest extends TestCase
{
    use RefreshDatabase;

    private const SEND_URL = 'https://api-sms.4jawaly.com/api/v1/account/area/sms/send';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.debug' => false,
            'services.fourjawaly.enabled' => true,
            'services.fourjawaly.base_url' => 'https://api-sms.4jawaly.com/api/v1',
            'services.fourjawaly.api_key' => 'test-key',
            'services.fourjawaly.api_secret' => 'test-secret',
            'services.fourjawaly.sender' => 'Taba3a',
        ]);
    }

    private function createActiveUser(): User
    {
        return User::create([
            'name' => 'أحمد فؤاد',
            'phone' => '0501112233',
            'phone_verified_at' => now(),
            'status' => 'active',
        ]);
    }

    public function test_login_sends_otp_via_4jawaly_and_it_can_be_verified(): void
    {
        Http::fake([self::SEND_URL => Http::response(['code' => 200, 'message' => 'تم الارسال', 'job_id' => 'abc'])]);
        $this->createActiveUser();

        $response = $this->postJson('/api/v1/auth/login', ['phone' => '0501112233']);

        $response->assertOk()->assertJsonPath('data.dev_otp', null);

        $storedOtp = OtpVerification::where('phone', '0501112233')->where('purpose', 'login')->firstOrFail();
        $this->assertSame('1234', $storedOtp->otp_code);

        Http::assertSent(function (Request $request) use ($storedOtp): bool {
            return $request->url() === self::SEND_URL
                && $request->hasHeader('Authorization', 'Basic '.base64_encode('test-key:test-secret'))
                && $request['messages'][0]['numbers'] === ['966501112233']
                && $request['messages'][0]['sender'] === 'Taba3a'
                && str_contains($request['messages'][0]['text'], $storedOtp->otp_code);
        });

        $this->postJson('/api/v1/auth/verify-login-otp', [
            'phone' => '0501112233',
            'otp_code' => $storedOtp->otp_code,
        ])->assertOk()->assertJsonStructure(['data' => ['access_token']]);
    }

    public function test_login_returns_error_and_stores_no_otp_when_4jawaly_fails(): void
    {
        Http::fake([self::SEND_URL => Http::response(['code' => 401, 'message' => 'Unauthorized'], 401)]);
        $this->createActiveUser();

        $this->postJson('/api/v1/auth/login', ['phone' => '0501112233'])
            ->assertStatus(503)
            ->assertJsonPath('success', false);

        $this->assertDatabaseCount('otp_verifications', 0);
    }

    public function test_no_sms_is_sent_when_4jawaly_is_disabled(): void
    {
        Http::fake();
        config(['services.fourjawaly.enabled' => false]);
        $this->createActiveUser();

        $this->postJson('/api/v1/auth/login', ['phone' => '0501112233'])->assertOk();

        Http::assertNothingSent();
        $this->assertDatabaseCount('otp_verifications', 1);
    }

    public function test_otp_requests_are_rate_limited_per_phone(): void
    {
        Http::fake([self::SEND_URL => Http::response(['code' => 200])]);
        $this->createActiveUser();

        foreach (range(1, 3) as $attempt) {
            $this->postJson('/api/v1/auth/login', ['phone' => '0501112233'])->assertOk();
        }

        $this->postJson('/api/v1/auth/login', ['phone' => '0501112233'])->assertStatus(429);
        Http::assertSentCount(3);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function phoneNumberProvider(): array
    {
        return [
            'local with leading zero' => ['0501112233', '966501112233'],
            'local without zero' => ['501112233', '966501112233'],
            'international with plus' => ['+966501112233', '966501112233'],
            'international with 00' => ['00966501112233', '966501112233'],
        ];
    }

    #[DataProvider('phoneNumberProvider')]
    public function test_phone_numbers_are_formatted_for_4jawaly(string $phone, string $expected): void
    {
        $this->assertSame($expected, app(FourJawalyService::class)->formatNumber($phone, '+966'));
    }
}
