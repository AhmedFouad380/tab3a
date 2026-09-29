<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\AppNotification;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = '🖨️ إدارة العمليات والطباعة';
    protected static ?string $modelLabel = 'طلب طباعة';
    protected static ?string $pluralModelLabel = 'طلبات الطباعة';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('معلومات الطلب')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('order_number')
                                    ->label('رقم الطلب')
                                    ->disabled(),
                                Forms\Components\Select::make('order_type')
                                    ->label('نوع الخدمة')
                                    ->options([
                                        'self_printing' => 'طباعة ذاتية فورية (Kiosk)',
                                        'pre_order' => 'طباعة مسبقة (Branch Pickup)',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('user_id')
                                    ->label('العميل')
                                    ->relationship('user', 'phone')
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Select::make('branch_id')
                                    ->label('الفرع')
                                    ->relationship('branch', 'name->ar')
                                    ->searchable(),
                                Forms\Components\Select::make('kiosk_machine_id')
                                    ->label('ماكينة الطباعة الذاتية')
                                    ->relationship('kiosk', 'machine_code')
                                    ->searchable(),
                                Forms\Components\DateTimePicker::make('scheduled_pickup_at')
                                    ->label('موعد الاستلام المجدول'),
                            ]),

                        Forms\Components\Section::make('الملفات المرفوعة ومواصفات الطباعة')
                            ->schema([
                                Forms\Components\Repeater::make('items')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('original_file_name')
                                            ->label('اسم الملف')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('detected_page_count')
                                            ->label('عدد الصفحات المقروء')
                                            ->numeric()
                                            ->disabled(),
                                        Forms\Components\TextInput::make('copies_count')
                                            ->label('عدد النسخ')
                                            ->numeric()
                                            ->required(),
                                        Forms\Components\Select::make('color_mode')
                                            ->label('الألوان')
                                            ->options([
                                                'black_and_white' => 'أبيض وأسود',
                                                'color' => 'ألوان',
                                            ]),
                                        Forms\Components\Select::make('side_mode')
                                            ->label('الوجه')
                                            ->options([
                                                'single_sided' => 'وجه واحد',
                                                'double_sided' => 'وجهين',
                                            ]),
                                        Forms\Components\TextInput::make('total_item_price')
                                            ->label('إجمالي سعر الملف')
                                            ->numeric()
                                            ->prefix('SAR'),
                                    ])
                                    ->columns(3)
                                    ->addable(false)
                                    ->deletable(false),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('الحالة والدفع')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('حالة الطلب')
                                    ->options([
                                        'pending' => 'قيد الانتظار (Pending)',
                                        'processing' => 'قيد التجهيز (Processing)',
                                        'printing' => 'جاري الطباعة (Printing)',
                                        'ready_for_pickup' => 'جاهز للاستلام (Ready)',
                                        'completed' => 'مكتمل ومستلم (Completed)',
                                        'cancelled' => 'ملغي (Cancelled)',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('payment_status')
                                    ->label('حالة الدفع')
                                    ->options([
                                        'unpaid' => 'غير مدفوع',
                                        'paid' => 'مدفوع',
                                        'refunded' => 'مسترجع',
                                        'failed' => 'فشلت العملية',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('payment_method')
                                    ->label('وسيلة الدفع')
                                    ->disabled(),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('المجموع الفرعي')
                                    ->numeric()
                                    ->prefix('SAR'),
                                Forms\Components\TextInput::make('tax_amount')
                                    ->label('ضريبة القيمة المضافة (VAT)')
                                    ->numeric()
                                    ->prefix('SAR'),
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('الإجمالي النهائي')
                                    ->numeric()
                                    ->prefix('SAR')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('user.phone')
                    ->label('جوال العميل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_type')
                    ->label('النوع')
                    ->formatStateUsing(fn (string $state) => $state === 'self_printing' ? '⚡ طباعة ذاتية' : '🏢 طباعة مسبقة')
                    ->badge()
                    ->color(fn (string $state) => $state === 'self_printing' ? 'info' : 'primary'),
                Tables\Columns\TextColumn::make('branch.name.ar')
                    ->label('الفرع / الماكينة')
                    ->placeholder(fn (Order $record) => $record->kiosk?->machine_code ?? '-'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('الإجمالي')
                    ->money('SAR')
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('الدفع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'danger',
                        'refunded' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ والوقت')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_type')
                    ->label('نوع الخدمة')
                    ->options([
                        'self_printing' => 'طباعة ذاتية',
                        'pre_order' => 'طباعة مسبقة',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'processing' => 'قيد التجهيز',
                        'ready_for_pickup' => 'جاهز للاستلام',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغي',
                    ]),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('الفرع')
                    ->relationship('branch', 'name->ar'),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_ready')
                    ->label('جاهز للاستلام')
                    ->icon('heroicon-o-bell-alert')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->order_type === 'pre_order' && in_array($record->status, ['pending', 'processing', 'printing']))
                    ->action(function (Order $record) {
                        $record->update(['status' => 'ready_for_pickup']);
                        
                        // إرسال إشعار للعميل
                        AppNotification::create([
                            'user_id' => $record->user_id,
                            'order_id' => $record->id,
                            'type' => 'order_ready',
                            'title' => [
                                'ar' => 'طلبك جاهز للاستلام! 🎉',
                                'en' => 'Your order is ready for pickup! 🎉',
                            ],
                            'body' => [
                                'ar' => "طلبك رقم {$record->order_number} تمت طباعته وجاهز للاستلام من فرع {$record->branch?->name['ar']}.",
                                'en' => "Your order #{$record->order_number} has been printed and is ready for pickup.",
                            ],
                        ]);

                        Notification::make()
                            ->title('تم تحديث حالة الطلب وإشعار العميل بنجاح')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
