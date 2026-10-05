<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_otp(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'أحمد فؤاد',
            'phone' => '0501112233',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'أحمد فؤاد',
            'phone' => '0501112233',
        ]);
    }

    public function test_cannot_register_if_phone_already_verified(): void
    {
        User::create([
            'name' => 'مستخدم سابق',
            'phone' => '0501112233',
            'phone_verified_at' => now(),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'مستخدم جديد',
            'phone' => '0501112233',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'رقم الهاتف مسجل بالفعل، يرجى تسجيل الدخول',
            ]);
    }

    public function test_user_can_verify_register_otp(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'أحمد فؤاد',
            'phone' => '0501112233',
        ]);

        $response = $this->postJson('/api/v1/auth/verify-register-otp', [
            'phone' => '0501112233',
            'otp_code' => '1234',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'access_token',
                    'token_type',
                    'user' => ['id', 'name', 'phone'],
                ],
            ]);

        $user = User::where('phone', '0501112233')->first();
        $this->assertNotNull($user->phone_verified_at);
    }

    public function test_login_fails_if_user_not_found(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'phone' => '0599999999',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'المستخدم غير موجود، يرجى إنشاء حساب جديد',
            ]);
    }

    public function test_user_can_login_and_receive_otp(): void
    {
        User::create([
            'name' => 'أحمد فؤاد',
            'phone' => '0501112233',
            'phone_verified_at' => now(),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'phone' => '0501112233',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_user_can_verify_login_otp(): void
    {
        User::create([
            'name' => 'أحمد فؤاد',
            'phone' => '0501112233',
            'phone_verified_at' => now(),
            'status' => 'active',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'phone' => '0501112233',
        ]);

        $response = $this->postJson('/api/v1/auth/verify-login-otp', [
            'phone' => '0501112233',
            'otp_code' => '1234',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'access_token',
                    'token_type',
                    'user' => ['id', 'name', 'phone'],
                ],
            ]);
    }

    public function test_authenticated_user_can_delete_account(): void
    {
        $user = User::create([
            'name' => 'أحمد فؤاد',
            'phone' => '0501112233',
            'phone_verified_at' => now(),
            'status' => 'active',
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/v1/auth/delete-account');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'تم حذف الحساب بنجاح',
            ]);

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }
}
