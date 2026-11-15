<?php

use App\Models\SystemSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'key' => 'pre_order_max_sheets',
                'group' => 'print_settings',
                'type' => 'string',
                'display_name' => [
                    'ar' => 'الحد الأقصى لعدد الأوراق للطباعة المسبقة',
                    'en' => 'Max Sheets for Pre-Order',
                ],
                'value' => [
                    'ar' => '500',
                    'en' => '500',
                ],
            ],
            [
                'key' => 'pre_order_min_order_price',
                'group' => 'pricing',
                'type' => 'string',
                'display_name' => [
                    'ar' => 'الحد الأدنى لقيمة طلب الطباعة المسبقة (ر.س)',
                    'en' => 'Minimum Pre-Order Price (SAR)',
                ],
                'value' => [
                    'ar' => '5.00',
                    'en' => '5.00',
                ],
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        SystemSetting::whereIn('key', [
            'pre_order_max_sheets',
            'pre_order_min_order_price',
        ])->delete();
    }
};
