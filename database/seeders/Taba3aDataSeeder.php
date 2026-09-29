<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchWorkingHour;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\FinishingOption;
use App\Models\KioskMachine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PricingRule;
use App\Models\StaticPage;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class Taba3aDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        SystemSetting::updateOrCreate(
            ['key' => 'app_name'],
            [
                'group' => 'general',
                'display_name' => ['ar' => 'اسم التطبيق', 'en' => 'App Name'],
                'value' => ['ar' => 'تطبيق طباعة', 'en' => 'Taba3a App'],
            ]
        );
        SystemSetting::updateOrCreate(
            ['key' => 'tax_rate_percentage'],
            [
                'group' => 'pricing',
                'display_name' => ['ar' => 'نسبة ضريبة القيمة المضافة (VAT %)', 'en' => 'Tax Rate %'],
                'value' => ['ar' => '15', 'en' => '15'],
            ]
        );
        SystemSetting::updateOrCreate(
            ['key' => 'max_file_size_mb'],
            [
                'group' => 'print_settings',
                'display_name' => ['ar' => 'الحد الأقصى لحجم الملف (MB)', 'en' => 'Max File Size (MB)'],
                'value' => ['ar' => '50', 'en' => '50'],
            ]
        );
        SystemSetting::updateOrCreate(
            ['key' => 'support_phone'],
            [
                'group' => 'contact',
                'display_name' => ['ar' => 'رقم الدعم الفني', 'en' => 'Support Phone'],
                'value' => ['ar' => '+966500000000', 'en' => '+966500000000'],
            ]
        );

        // 2. Branches
        $branch1 = Branch::create([
            'name' => ['ar' => 'فرع الرياض - حي العليا', 'en' => 'Riyadh Branch - Olaya'],
            'address' => ['ar' => 'طريق الملك فهد، حي العليا، الرياض', 'en' => 'King Fahd Rd, Olaya, Riyadh'],
            'city' => 'الرياض',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'phone' => '+966112345678',
            'whatsapp' => '+966501234567',
            'email' => 'olaya@taba3a.com',
            'is_active' => true,
            'allows_pre_order' => true,
            'daily_capacity' => 150,
        ]);

        $branch2 = Branch::create([
            'name' => ['ar' => 'فرع جدة - طريق الملك', 'en' => 'Jeddah Branch - King Road'],
            'address' => ['ar' => 'طريق الملك عبدالعزيز، حي الروضة، جدة', 'en' => 'King Abdulaziz Rd, Rawdah, Jeddah'],
            'city' => 'جدة',
            'latitude' => 21.5433,
            'longitude' => 39.1728,
            'phone' => '+966122345678',
            'whatsapp' => '+966507654321',
            'email' => 'jeddah@taba3a.com',
            'is_active' => true,
            'allows_pre_order' => true,
            'daily_capacity' => 100,
        ]);

        // Branch Working Hours
        foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'] as $day) {
            BranchWorkingHour::create([
                'branch_id' => $branch1->id,
                'day_of_week' => $day,
                'opening_time' => '08:00:00',
                'closing_time' => '22:00:00',
                'is_day_off' => false,
            ]);
        }

        // 3. Kiosk Machines
        $kiosk1 = KioskMachine::create([
            'branch_id' => $branch1->id,
            'machine_code' => 'KSK-101',
            'qr_token' => 'QR-TB-101-RYD',
            'nfc_tag_id' => 'NFC-04-A1-B2-C3',
            'name' => ['ar' => 'ماكينة مكتبة الملك فهد', 'en' => 'King Fahd Library Kiosk'],
            'location_description' => ['ar' => 'الدور الأرضي - بجوار المدخل الرئيسي', 'en' => 'Ground Floor - Main Entrance'],
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'status' => 'online',
            'last_ping_at' => now(),
            'supports_color' => true,
            'supports_duplex' => true,
            'supported_paper_sizes' => ['A4', 'A3'],
            'paper_tray_a4_sheets' => 450,
            'paper_tray_a3_sheets' => 100,
            'black_toner_level' => 92,
            'cyan_toner_level' => 88,
            'magenta_toner_level' => 85,
            'yellow_toner_level' => 90,
        ]);

        $kiosk2 = KioskMachine::create([
            'branch_id' => $branch2->id,
            'machine_code' => 'KSK-202',
            'qr_token' => 'QR-TB-202-JED',
            'nfc_tag_id' => 'NFC-04-D4-E5-F6',
            'name' => ['ar' => 'ماكينة مجمع العرب', 'en' => 'Mall of Arabia Kiosk'],
            'location_description' => ['ar' => 'بوابة 3 - الدور الأول', 'en' => 'Gate 3 - First Floor'],
            'latitude' => 21.5433,
            'longitude' => 39.1728,
            'status' => 'online',
            'last_ping_at' => now(),
            'supports_color' => true,
            'supports_duplex' => true,
            'supported_paper_sizes' => ['A4'],
            'paper_tray_a4_sheets' => 320,
            'paper_tray_a3_sheets' => 0,
            'black_toner_level' => 78,
            'cyan_toner_level' => 70,
            'magenta_toner_level' => 75,
            'yellow_toner_level' => 80,
        ]);

        // 4. Pricing Rules
        PricingRule::create([
            'service_type' => 'all',
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
            'paper_type' => 'plain_80g',
            'price_per_page' => 0.50,
            'is_active' => true,
        ]);
        PricingRule::create([
            'service_type' => 'all',
            'paper_size' => 'A4',
            'color_mode' => 'color',
            'side_mode' => 'single_sided',
            'paper_type' => 'plain_80g',
            'price_per_page' => 1.50,
            'is_active' => true,
        ]);
        PricingRule::create([
            'service_type' => 'all',
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'double_sided',
            'paper_type' => 'plain_80g',
            'price_per_page' => 0.80,
            'is_active' => true,
        ]);

        // 5. Finishing Options
        FinishingOption::create([
            'name' => ['ar' => 'تجليد حلزوني سلك', 'en' => 'Spiral Wire Binding'],
            'description' => ['ar' => 'تجليد حلزوني مع غلاف بلاستيك شفاف مقوى', 'en' => 'Spiral binding with plastic cover'],
            'code' => 'spiral_binding',
            'base_price' => 5.00,
            'available_for_pre_order' => true,
            'available_for_self_print' => false,
            'is_active' => true,
        ]);
        FinishingOption::create([
            'name' => ['ar' => 'تدبيس زاوية مجاني', 'en' => 'Corner Stapling'],
            'description' => ['ar' => 'تدبيس أنيق في زاوية المستند', 'en' => 'Neat stapling on document corner'],
            'code' => 'stapling',
            'base_price' => 0.00,
            'available_for_pre_order' => true,
            'available_for_self_print' => true,
            'is_active' => true,
        ]);

        // 6. Users
        $user1 = User::create([
            'name' => 'إسراء أحمد',
            'phone_country_code' => '+966',
            'phone' => '+966551234567',
            'email' => 'esraa@example.com',
            'preferred_locale' => 'ar',
            'wallet_balance' => 50.00,
            'status' => 'active',
        ]);

        $user2 = User::create([
            'name' => 'محمد خالد',
            'phone_country_code' => '+966',
            'phone' => '+966559876543',
            'email' => 'mohammed@example.com',
            'preferred_locale' => 'ar',
            'wallet_balance' => 20.00,
            'status' => 'active',
        ]);

        // 7. Orders
        $order1 = Order::create([
            'order_number' => 'TAB-' . date('Ymd') . '-001',
            'user_id' => $user1->id,
            'order_type' => 'self_printing',
            'kiosk_machine_id' => $kiosk1->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'apple_pay',
            'subtotal' => 4.00,
            'tax_amount' => 0.60,
            'total_amount' => 4.60,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'original_file_name' => 'محاضرة.pdf',
            'file_path' => 'uploads/sample_lecture.pdf',
            'file_extension' => 'pdf',
            'file_size_bytes' => 4404019, // 4.2 MB
            'detected_page_count' => 8,
            'pages_to_print_count' => 8,
            'page_range_selection' => 'all',
            'paper_size' => 'A4',
            'color_mode' => 'black_and_white',
            'side_mode' => 'single_sided',
            'copies_count' => 1,
            'total_sheets_needed' => 8,
            'unit_price_per_page' => 0.50,
            'total_item_price' => 4.00,
        ]);

        $order2 = Order::create([
            'order_number' => 'TAB-' . date('Ymd') . '-002',
            'user_id' => $user2->id,
            'order_type' => 'pre_order',
            'branch_id' => $branch1->id,
            'scheduled_pickup_at' => now()->addHours(3),
            'status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'subtotal' => 25.00,
            'tax_amount' => 3.75,
            'total_amount' => 28.75,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'original_file_name' => 'مشروع_التخرج_النهائي.pdf',
            'file_path' => 'uploads/graduation_project.pdf',
            'file_extension' => 'pdf',
            'file_size_bytes' => 12582912,
            'detected_page_count' => 40,
            'pages_to_print_count' => 40,
            'page_range_selection' => 'all',
            'paper_size' => 'A4',
            'color_mode' => 'color',
            'side_mode' => 'double_sided',
            'copies_count' => 1,
            'total_sheets_needed' => 20,
            'unit_price_per_page' => 1.00,
            'total_item_price' => 25.00,
        ]);

        // 8. Static Pages
        StaticPage::create([
            'slug' => 'about-us',
            'title' => ['ar' => 'من نحن', 'en' => 'About Us'],
            'content' => [
                'ar' => '<p>تطبيق طباعة هو منصتك الذكية الرائدة لخدمات الطباعة الفورية والذاتية عبر شبكة ماكينات وفروع منتشرة.</p>',
                'en' => '<p>Taba3a is your leading smart printing platform offering instant self-service and branch pickup.</p>',
            ],
            'is_published' => true,
        ]);

        StaticPage::create([
            'slug' => 'privacy-policy',
            'title' => ['ar' => 'سياسة الخصوصية', 'en' => 'Privacy Policy'],
            'content' => [
                'ar' => '<p>نحن نحرص على حماية سرية مستنداتك وملفاتك ويتم مسح الملفات فور انتهاء عملية الطباعة.</p>',
                'en' => '<p>We take your privacy seriously. Files are automatically removed after printing.</p>',
            ],
            'is_published' => true,
        ]);

        StaticPage::create([
            'slug' => 'terms-and-conditions',
            'title' => ['ar' => 'الشروط والأحكام', 'en' => 'Terms and Conditions'],
            'content' => [
                'ar' => '<p>الشروط والأحكام الخاصة باستخدام تطبيق وماكينات طباعة...</p>',
                'en' => '<p>Terms and conditions for using Taba3a platform and kiosks...</p>',
            ],
            'is_published' => true,
        ]);

        // 9. FAQs
        Faq::create([
            'category' => 'self_printing',
            'question' => ['ar' => 'كيف أستخدم ماكينة الطباعة الذاتية؟', 'en' => 'How to use the self-printing kiosk?'],
            'answer' => [
                'ar' => 'قم برفع الملف وتحديد المواصفات ثم امسح رمز الـ QR على الماكينة أو قرّب هاتفك بتقنية NFC واضغط طباعة.',
                'en' => 'Upload your file, configure settings, scan the QR code on the kiosk or tap NFC, then print.',
            ],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Faq::create([
            'category' => 'pre_order',
            'question' => ['ar' => 'متى يمكنني استلام طلبي من الفرع؟', 'en' => 'When can I pick up my order from the branch?'],
            'answer' => [
                'ar' => 'يمكنك اختيار الوقت المناسب لك أثناء الطلب، وسنرسل لك إشعاراً فور اكتمال التجهيز والطباعة.',
                'en' => 'Choose your pickup time during ordering. We will send a notification once ready.',
            ],
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // 10. Contact Message
        ContactMessage::create([
            'user_id' => $user1->id,
            'name' => 'إسراء أحمد',
            'phone' => '+966551234567',
            'email' => 'esraa@example.com',
            'subject' => 'استفسار عن خدمة التجليد الفاخر',
            'message' => 'هل يتوفر لديكم تجليد مقوى جلدي مع طباعة ذهبية؟',
            'status' => 'new',
        ]);
    }
}
