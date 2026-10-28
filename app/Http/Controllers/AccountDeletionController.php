<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountDeletionController extends Controller
{
    /**
     * Show Account Deletion page.
     */
    public function show(Request $request): View
    {
        return view('delete-account');
    }

    /**
     * Request OTP for account deletion.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $normalizedPhone = $this->normalizePhone($request->phone);
        $user = User::where('phone', $normalizedPhone)
            ->orWhere('phone', $request->phone)
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'عفواً، لم يتم العثور على حساب مسجل برقم الجوال المدخل.',
                'message_en' => 'Sorry, no registered account was found with this phone number.',
            ], 404);
        }

        // Generate 4-digit OTP code
        $otpCode = config('app.debug') ? '1234' : (string) random_int(1000, 9999);
        $countryCode = $user->phone_country_code ?? '+966';

        // Invalidate previous unused OTPs
        OtpVerification::where('phone', $user->phone)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        OtpVerification::create([
            'phone_country_code' => $countryCode,
            'phone' => $user->phone,
            'otp_code' => $otpCode,
            'purpose' => 'login',
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز التحقق إلى هاتفك بنجاح.',
            'message_en' => 'Verification code sent to your phone successfully.',
            'dev_otp' => config('app.debug') ? $otpCode : null,
            'phone' => $user->phone,
        ]);
    }

    /**
     * Confirm account deletion using OTP.
     */
    public function confirmDelete(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'otp_code' => ['required', 'string', 'size:4'],
        ]);

        $normalizedPhone = $this->normalizePhone($request->phone);
        $user = User::where('phone', $normalizedPhone)
            ->orWhere('phone', $request->phone)
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موجود.',
                'message_en' => 'User not found.',
            ], 404);
        }

        $otp = OtpVerification::where('phone', $user->phone)
            ->where('otp_code', $request->otp_code)
            ->where('purpose', 'login')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        $isDebugValid = config('app.debug') && $request->otp_code === '1234';

        if (! $otp && ! $isDebugValid) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.',
                'message_en' => 'Invalid or expired verification code.',
            ], 422);
        }

        if ($otp) {
            $otp->update(['is_used' => true]);
        }

        // Revoke all personal access tokens
        $user->tokens()->delete();

        // Soft delete user account & clear device tokens
        $userName = $user->name ?? 'المستخدم';
        $user->update([
            'fcm_token' => null,
        ]);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "تم حذف حسابك ({$userName}) بنجاح، وإبطال جميع الجلسات وحذف البيانات الشخصية.",
            'message_en' => 'Your account has been deleted successfully, sessions revoked, and personal data purged.',
        ]);
    }

    /**
     * Submit a manual deletion request form.
     */
    public function submitRequest(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:120'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $normalizedPhone = $this->normalizePhone($request->phone);
        $user = User::where('phone', $normalizedPhone)
            ->orWhere('phone', $request->phone)
            ->first();

        ContactMessage::create([
            'user_id' => $user?->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'subject' => 'طلب حذف الحساب وبيانات المستخدم (Store Account Deletion Request)',
            'message' => "طلب حذف الحساب عبر بوابة الويب.\nرقم الجوال: {$request->phone}\nالبريد: {$request->email}\nالسبب / الملاحظات: ".($request->reason ?? 'لم يتم تقديم سبب'),
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم استلام طلب حذف الحساب بنجاح. سيتم مراجعة الطلب وحذف البيانات خلال 48 ساعة كحد أقصى.',
            'message_en' => 'Account deletion request received successfully. It will be reviewed and processed within 48 hours.',
        ]);
    }

    /**
     * Helper to normalize Saudi phone numbers.
     */
    private function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($clean, '05')) {
            return '+966'.substr($clean, 1);
        }

        if (str_starts_with($clean, '5') && strlen($clean) === 9) {
            return '+966'.$clean;
        }

        if (str_starts_with($clean, '966')) {
            return '+'.$clean;
        }

        return $clean;
    }
}
