<?php

namespace App\Filament\Widgets;

use App\Models\KioskMachine;
use App\Models\KioskMaintenanceAlert;
use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $selfPrintOrders = Order::where('order_type', 'self_printing')->count();
        $preOrders = Order::where('order_type', 'pre_order')->count();
        $activeKiosks = KioskMachine::where('status', 'online')->count();
        $unresolvedAlerts = KioskMaintenanceAlert::where('is_resolved', false)->count();

        return [
            Stat::make('إجمالي المبيعات المؤكدة', number_format($totalRevenue, 2) . ' SAR')
                ->description('المبيعات من الطباعة الذاتية والمسبقة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('طلبات الطباعة الذاتية ⚡', $selfPrintOrders)
                ->description('طباعة فورية عبر الماكينات')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('info'),

            Stat::make('طلبات الفروع المسبقة 🏢', $preOrders)
                ->description('طلبات استلام من الفروع')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('primary'),

            Stat::make('الماكينات النشطة / التنبيهات', "{$activeKiosks} ماكينة / {$unresolvedAlerts} تنبيه")
                ->description($unresolvedAlerts > 0 ? 'يوجد ماكينات تحتاج صيانة أو ورق' : 'كافة الماكينات تعمل بشكل ممتاز')
                ->descriptionIcon('heroicon-m-printer')
                ->color($unresolvedAlerts > 0 ? 'danger' : 'success'),
        ];
    }
}
