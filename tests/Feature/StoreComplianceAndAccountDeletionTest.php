<?php

namespace Tests\Feature;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreComplianceAndAccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_page_loads_successfully(): void
    {
        $response = $this->get('/privacy');

        $response->assertStatus(200)
            ->assertSee('سياسة الخصوصية')
            ->assertSee('منصة طباعة')
            ->assertSee('Privacy Policy');

        // Also test alternate URL
        $altResponse = $this->get('/privacy-policy');
        $altResponse->assertStatus(200);
    }

    public function test_delete_account_page_loads_and_redirects_alternate_slug(): void
    {
        $response = $this->get('/delete-account');

        $response->assertStatus(200)
            ->assertSee('حذف الحساب')
            ->assertSee('رمز التحقق')
            ->assertSee('Account Deletion');

        // Test snake_case redirect /delete_account
        $redirectResponse = $this->get('/delete_account');
        $redirectResponse->assertRedirect('/delete-account');
    }

    public function test_user_can_request_otp_for_account_deletion(): void
    {
        User::create([
            'name' => 'سارة علي',
            'phone' => '+966551234567',
            'status' => 'active',
        ]);

        $response = $this->postJson('/delete-account/send-otp', [
            'phone' => '0551234567',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('otp_verifications', [
            'phone' => '+966551234567',
            'purpose' => 'login',
            'is_used' => false,
        ]);
    }

    public function test_cannot_request_deletion_otp_for_non_existent_phone(): void
    {
        $response = $this->postJson('/delete-account/send-otp', [
            'phone' => '0599999999',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_user_can_confirm_account_deletion_with_valid_otp(): void
    {
        $user = User::create([
            'name' => 'عبدالله خالد',
            'phone' => '+966559876543',
            'status' => 'active',
        ]);

        $token = $user->createToken('test_token')->plainTextToken;
        $this->assertCount(1, $user->tokens);

        OtpVerification::create([
            'phone_country_code' => '+966',
            'phone' => '+966559876543',
            'otp_code' => '4567',
            'purpose' => 'login',
            'expires_at' => now()->addMinutes(5),
            'is_used' => false,
        ]);

        $response = $this->postJson('/delete-account/confirm', [
            'phone' => '0559876543',
            'otp_code' => '4567',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // User should be soft deleted
        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);

        // Tokens revoked
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_cannot_confirm_deletion_with_invalid_otp(): void
    {
        $user = User::create([
            'name' => 'فيصل الحربي',
            'phone' => '+966558887766',
            'status' => 'active',
        ]);

        OtpVerification::create([
            'phone_country_code' => '+966',
            'phone' => '+966558887766',
            'otp_code' => '9999',
            'purpose' => 'login',
            'expires_at' => now()->addMinutes(5),
            'is_used' => false,
        ]);

        $response = $this->postJson('/delete-account/confirm', [
            'phone' => '0558887766',
            'otp_code' => '1111',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertNotSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    public function test_user_can_submit_manual_deletion_request(): void
    {
        $response = $this->postJson('/delete-account/request', [
            'name' => 'منيرة السعد',
            'phone' => '0553334455',
            'email' => 'muneera@example.com',
            'reason' => 'أرغب بحذف كافة بياناتي بصورة نهائية',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'منيرة السعد',
            'phone' => '0553334455',
            'email' => 'muneera@example.com',
            'status' => 'new',
        ]);
    }
}
