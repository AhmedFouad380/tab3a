<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '⚡ أحدث طلبات الطباعة اللحظية';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::latest()->limit(8))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('user.phone')
                    ->label('جوال العميل'),
                Tables\Columns\TextColumn::make('order_type')
                    ->label('نوع الخدمة')
                    ->formatStateUsing(fn (string $state) => $state === 'self_printing' ? '⚡ طباعة ذاتية' : '🏢 طباعة مسبقة')
                    ->badge()
                    ->color(fn (string $state) => $state === 'self_printing' ? 'info' : 'primary'),
                Tables\Columns\TextColumn::make('branch.name.ar')
                    ->label('الفرع / الماكينة')
                    ->placeholder(fn (Order $record) => $record->kiosk?->machine_code ?? '-'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('الإجمالي')
                    ->money('SAR'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'ready_for_pickup' => 'info',
                        'processing', 'printing' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('الوقت')
                    ->since(),
            ]);
    }
}
