<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\ContactMessageRequest;
use App\Http\Resources\Api\V1\FaqResource;
use App\Http\Resources\Api\V1\StaticPageResource;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Order;
use App\Models\StaticPage;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends BaseApiController
{
    /**
     * Home Screen Data
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');
        $locale = $this->getLocale();

        $greeting = $locale === 'en'
            ? 'Good evening' . ($user && $user->name ? ', ' . $user->name : '')
            : 'مساء الخير' . ($user && $user->name ? ' ، ' . $user->name : '');

        $subGreeting = $locale === 'en'
            ? 'What would you like to print today?'
            : 'وش حابة تطبعي اليوم؟';

        $activeOrdersCount = $user
            ? Order::where('user_id', $user->id)->whereIn('status', ['pending', 'processing', 'printing', 'ready_for_pickup'])->count()
            : 0;

        $services = [
            [
                'id' => 'self_printing',
                'title' => $locale === 'en' ? 'Self Printing' : 'الطباعة الذاتية',
                'subtitle' => $locale === 'en' ? 'Print instantly from nearest kiosk' : 'اطبع الآن من أقرب ماكينة',
                'icon' => 'printer',
                'route' => '/self-printing/upload',
            ],
            [
                'id' => 'pre_order',
                'title' => $locale === 'en' ? 'Pre-Order Printing' : 'الطباعة المسبقة',
                'subtitle' => $locale === 'en' ? 'Choose branch and pickup time' : 'اختر الفرع و وقت الإستلام المناسب',
                'icon' => 'calendar-pin',
                'route' => '/pre-order/upload',
            ],
        ];

        return $this->success([
            'greeting' => $greeting,
            'sub_greeting' => $subGreeting,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'wallet_balance' => (float) $user->wallet_balance,
            ] : null,
            'services' => $services,
            'active_orders_count' => $activeOrdersCount,
        ]);
    }

    /**
     * General App Settings
     */
    public function getSettings(): JsonResponse
    {
        $settings = SystemSetting::all();
        $formatted = [];

        foreach ($settings as $setting) {
            $formatted[$setting->key] = $this->localize($setting->value);
        }

        return $this->success($formatted);
    }

    /**
     * Get Static Pages (Returns full list of all active pages, or single page if slug provided)
     */
    public function getPages(Request $request): JsonResponse
    {
        $query = StaticPage::where('is_published', true);

        if ($request->filled('slug')) {
            $page = $query->where('slug', $request->slug)->first();
            if (!$page) {
                return $this->error($this->getLocale() === 'en' ? 'Page not found' : 'الصفحة غير موجودة', 404);
            }

            return $this->success(new StaticPageResource($page));
        }

        $pages = $query->get();
        return $this->success(StaticPageResource::collection($pages));
    }

    /**
     * Get FAQs
     */
    public function getFaqs(Request $request): JsonResponse
    {
        $query = Faq::where('is_active', true)->orderBy('sort_order', 'asc');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $faqs = $query->get();
        return $this->success(FaqResource::collection($faqs));
    }

    /**
     * Submit Contact Message
     */
    public function submitContact(ContactMessageRequest $request): JsonResponse
    {
        $user = $request->user('sanctum');

        $contact = ContactMessage::create([
            'user_id' => $user?->id,
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'new',
        ]);

        $message = $this->getLocale() === 'en'
            ? 'Your message has been sent successfully. We will get back to you shortly.'
            : 'تم إرسال رسالتك بنجاح، سيتواصل معك فريق الدعم قريباً.';

        return $this->success(['message_id' => $contact->id], $message);
    }
}
