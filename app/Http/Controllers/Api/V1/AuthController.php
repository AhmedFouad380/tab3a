<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\CompleteProfileRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\ResendOtpRequest;
use App\Http\Requests\Api\V1\SendOtpRequest;
use App\Http\Requests\Api\V1\UpdateProfileRequest;
use App\Http\Requests\Api\V1\VerifyOtpRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseApiController
{
    /**
     * Register a new user and send OTP
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $phone = $request->phone;
        $countryCode = $request->phone_country_code ?? '+966';

        $existingUser = User::where('phone', $phone)->first();
        if ($existingUser && $existingUser->phone_verified_at !== null) {
            $errMsg = $this->getLocale() === 'en'
                ? 'Phone number is already registered, please log in'
                : 'رقم الهاتف مسجل بالفعل، يرجى تسجيل الدخول';
            return $this->error($errMsg, 422);
        }

        if (!$existingUser) {
            $existingUser = User::create([
                'name' => $request->name,
                'phone' => $phone,
                'phone_country_code' => $countryCode,
                'preferred_locale' => $this->getLocale(),
                'status' => 'active',
            ]);
        } else {
            $existingUser->update([
                'name' => $request->name,
                'phone_country_code' => $countryCode,
                'preferred_locale' => $this->getLocale(),
            ]);
        }

        $otpCode = '1234'; // Default for test mode or SMS provider
        OtpVerification::create([
            'phone_country_code' => $countryCode,
            'phone' => $phone,
            'otp_code' => $otpCode,
            'purpose' => 'register',
            'expires_at' => now()->addMinutes(5),
            'is_used' => false,
        ]);

        $message = $this->getLocale() === 'en'
            ? 'Verification code sent successfully'
            : 'تم إرسال رمز التحقق بنجاح';

        return $this->success([
            'phone' => $phone,
            'phone_country_code' => $countryCode,
            'dev_otp' => config('app.debug') ? $otpCode : null,
        ], $message);
    }

    /**
     * Verify Register OTP and issue access token
     */
    public function verifyRegisterOtp(VerifyOtpRequest $request): JsonResponse
    {
        $otp = OtpVerification::where('phone', $request->phone)
            ->where('otp_code', $request->otp_code)
            ->where('purpose', 'register')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp && !(config('app.debug') && $request->otp_code === '1234')) {
            $errMsg = $this->getLocale() === 'en'
                ? 'Invalid or expired verification code'
                : 'رمز التحقق غير صحيح أو منتهي الصلاحية';
            return $this->error($errMsg, 400);
        }

        if ($otp) {
            $otp->update(['is_used' => true]);
        }

        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            $errMsg = $this->getLocale() === 'en' ? 'User not found' : 'المستخدم غير موجود';
            return $this->error($errMsg, 404);
        }

        $user->update([
            'phone_verified_at' => now(),
            'fcm_token' => $request->fcm_token ?? $user->fcm_token,
        ]);

        $token = $user->createToken('mobile_app')->plainTextToken;
        $message = $this->getLocale() === 'en' ? 'Account verified successfully' : 'تم تفعيل الحساب بنجاح';

        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], $message);
    }

    /**
     * Login request and send OTP
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $phone = $request->phone;
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            $errMsg = $this->getLocale() === 'en'
                ? 'User not found, please create an account'
                : 'المستخدم غير موجود، يرجى إنشاء حساب جديد';
            return $this->error($errMsg, 404);
        }

        if ($user->status === 'blocked') {
            $errMsg = $this->getLocale() === 'en'
                ? 'Account is suspended'
                : 'هذا الحساب محظور، يرجى التواصل مع الإدارة';
            return $this->error($errMsg, 403);
        }

        $countryCode = $request->phone_country_code ?? $user->phone_country_code ?? '+966';
        $otpCode = '1234';

        OtpVerification::create([
            'phone_country_code' => $countryCode,
            'phone' => $phone,
            'otp_code' => $otpCode,
            'purpose' => 'login',
            'expires_at' => now()->addMinutes(5),
            'is_used' => false,
        ]);

        $message = $this->getLocale() === 'en'
            ? 'Verification code sent successfully'
            : 'تم إرسال رمز التحقق بنجاح';

        return $this->success([
            'phone' => $phone,
            'phone_country_code' => $countryCode,
            'dev_otp' => config('app.debug') ? $otpCode : null,
        ], $message);
    }

    /**
     * Verify Login OTP and issue access token
     */
    public function verifyLoginOtp(VerifyOtpRequest $request): JsonResponse
    {
        $otp = OtpVerification::where('phone', $request->phone)
            ->where('otp_code', $request->otp_code)
            ->where('purpose', 'login')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp && !(config('app.debug') && $request->otp_code === '1234')) {
            $errMsg = $this->getLocale() === 'en'
                ? 'Invalid or expired verification code'
                : 'رمز التحقق غير صحيح أو منتهي الصلاحية';
            return $this->error($errMsg, 400);
        }

        if ($otp) {
            $otp->update(['is_used' => true]);
        }

        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            $errMsg = $this->getLocale() === 'en' ? 'User not found' : 'المستخدم غير موجود';
            return $this->error($errMsg, 404);
        }

        if ($user->status === 'blocked') {
            return $this->error($this->getLocale() === 'en' ? 'Account is suspended' : 'هذا الحساب محظور، يرجى التواصل مع الإدارة', 403);
        }

        if ($request->filled('fcm_token')) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        $token = $user->createToken('mobile_app')->plainTextToken;
        $message = $this->getLocale() === 'en' ? 'Logged in successfully' : 'تم تسجيل الدخول بنجاح';

        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], $message);
    }

    /**
     * Resend OTP for login or registration
     */
    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        $phone = $request->phone;
        $type = $request->type ?? 'login';
        $countryCode = $request->phone_country_code ?? '+966';

        if ($type === 'login') {
            $user = User::where('phone', $phone)->first();
            if (!$user) {
                $errMsg = $this->getLocale() === 'en'
                    ? 'User not found, please create an account'
                    : 'المستخدم غير موجود، يرجى إنشاء حساب جديد';
                return $this->error($errMsg, 404);
            }
            if ($user->status === 'blocked') {
                return $this->error($this->getLocale() === 'en' ? 'Account is suspended' : 'هذا الحساب محظور، يرجى التواصل مع الإدارة', 403);
            }
        }

        $otpCode = '1234';
        OtpVerification::create([
            'phone_country_code' => $countryCode,
            'phone' => $phone,
            'otp_code' => $otpCode,
            'purpose' => $type,
            'expires_at' => now()->addMinutes(5),
            'is_used' => false,
        ]);

        $message = $this->getLocale() === 'en'
            ? 'Verification code sent successfully'
            : 'تم إرسال رمز التحقق بنجاح';

        return $this->success([
            'phone' => $phone,
            'phone_country_code' => $countryCode,
            'dev_otp' => config('app.debug') ? $otpCode : null,
        ], $message);
    }

    /**
     * Legacy Send OTP to phone
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $phone = $request->phone;
        $countryCode = $request->phone_country_code ?? '+966';
        $otpCode = '1234';

        OtpVerification::create([
            'phone_country_code' => $countryCode,
            'phone' => $phone,
            'otp_code' => $otpCode,
            'purpose' => 'login',
            'expires_at' => now()->addMinutes(5),
            'is_used' => false,
        ]);

        $message = $this->getLocale() === 'en'
            ? 'Verification code sent successfully'
            : 'تم إرسال رمز التحقق بنجاح';

        return $this->success([
            'phone' => $phone,
            'phone_country_code' => $countryCode,
            'dev_otp' => config('app.debug') ? $otpCode : null,
        ], $message);
    }

    /**
     * Legacy Verify OTP & Login / Register
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $otp = OtpVerification::where('phone', $request->phone)
            ->where('otp_code', $request->otp_code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp && !(config('app.debug') && $request->otp_code === '1234')) {
            $errMsg = $this->getLocale() === 'en'
                ? 'Invalid or expired verification code'
                : 'رمز التحقق غير صحيح أو منتهي الصلاحية';
            return $this->error($errMsg, 400);
        }

        if ($otp) {
            $otp->update(['is_used' => true]);
        }

        $user = User::where('phone', $request->phone)->first();
        $isNewUser = false;

        if (!$user) {
            $user = User::create([
                'phone' => $request->phone,
                'phone_verified_at' => now(),
                'fcm_token' => $request->fcm_token,
                'preferred_locale' => $this->getLocale(),
                'status' => 'active',
            ]);
            $isNewUser = true;
        } else {
            if ($request->filled('fcm_token')) {
                $user->update(['fcm_token' => $request->fcm_token]);
            }
        }

        if ($user->status === 'blocked') {
            return $this->error($this->getLocale() === 'en' ? 'Account is suspended' : 'هذا الحساب محظور، يرجى التواصل مع الإدارة', 403);
        }

        $token = $user->createToken('mobile_app')->plainTextToken;
        $message = $this->getLocale() === 'en' ? 'Logged in successfully' : 'تم تسجيل الدخول بنجاح';

        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'is_new_user' => $isNewUser || empty($user->name),
            'user' => new UserResource($user),
        ], $message);
    }

    /**
     * Complete profile for new users
     */
    public function completeProfile(CompleteProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email ?? $user->email,
            'preferred_locale' => $request->preferred_locale ?? $user->preferred_locale,
        ]);

        $message = $this->getLocale() === 'en' ? 'Profile completed successfully' : 'تم حفظ البيانات بنجاح';

        return $this->success([
            'user' => new UserResource($user),
        ], $message);
    }

    /**
     * Get Profile
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user()->loadCount('orders');
        return $this->success(new UserResource($user));
    }

    /**
     * Update Profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->only(['name', 'email', 'preferred_locale', 'fcm_token']));

        $message = $this->getLocale() === 'en' ? 'Profile updated successfully' : 'تم تحديث الملف الشخصي بنجاح';

        return $this->success(new UserResource($user), $message);
    }

    /**
     * Delete user account (Soft delete & revoke tokens)
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revoke all tokens
        $user->tokens()->delete();

        // Soft delete user
        $user->delete();

        $message = $this->getLocale() === 'en'
            ? 'Account deleted successfully'
            : 'تم حذف الحساب بنجاح';

        return $this->success(null, $message);
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        $message = $this->getLocale() === 'en' ? 'Logged out successfully' : 'تم تسجيل الخروج بنجاح';
        return $this->success(null, $message);
    }
}

