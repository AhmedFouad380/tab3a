<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KioskMachineResource\Pages;
use App\Models\KioskMachine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KioskMachineResource extends Resource
{
    protected static ?string $model = KioskMachine::class;

    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationGroup = '🖨️ إدارة العمليات والطباعة';
    protected static ?string $modelLabel = 'ماكينة طباعة ذاتية';
    protected static ?string $pluralModelLabel = 'ماكينات الطباعة الذاتية (Kiosks)';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الماكينة والموقع')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('machine_code')
                            ->label('كود الماكينة')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g. KSK-001'),
                        Forms\Components\Select::make('branch_id')
                            ->label('الفرع / الموقع التابع له')
                            ->relationship('branch', 'name->ar')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name.ar')
                            ->label('اسم الماكينة (عربي)')
                            ->required(),
                        Forms\Components\TextInput::make('name.en')
                            ->label('Machine Name (English)')
                            ->required(),
                        Forms\Components\TextInput::make('qr_token')
                            ->label('رمز الـ QR Token')
                            ->default(fn () => 'QR-' . strtoupper(bin2hex(random_bytes(4))))
                            ->required(),
                        Forms\Components\TextInput::make('nfc_tag_id')
                            ->label('معرف الـ NFC Tag ID')
                            ->placeholder('NFC-XXXX-XXXX'),
                        Forms\Components\Select::make('status')
                            ->label('حالة الماكينة اللحظية')
                            ->options([
                                'online' => 'متصلة وجاهزة (Online)',
                                'busy' => 'مشغولة بالطباعة (Busy)',
                                'maintenance' => 'تحت الصيانة (Maintenance)',
                                'offline' => 'غير متصلة (Offline)',
                            ])
                            ->default('online')
                            ->required(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('عنوان الـ IP للماكينة'),
                    ]),

                Forms\Components\Section::make('رصيد الورق والإمدادات (Supplies)')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('paper_tray_a4_sheets')
                            ->label('رصيد ورق A4 المتاح (عدد الأوراق)')
                            ->numeric()
                            ->default(500),
                        Forms\Components\TextInput::make('paper_tray_a3_sheets')
                            ->label('رصيد ورق A3 المتاح')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('supports_color')
                            ->label('تدعم الطباعة الملونة')
                            ->default(true),
                        Forms\Components\Toggle::make('supports_duplex')
                            ->label('تدعم الطباعة وجهين (Duplex)')
                            ->default(true),
                    ]),

                Forms\Components\Section::make('مستويات الحبر (CMYK Toner Levels %)')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('black_toner_level')
                            ->label('الحبر الأسود (Black %)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(100)
                            ->suffix('%'),
                        Forms\Components\TextInput::make('cyan_toner_level')
                            ->label('الحبر السماوي (Cyan %)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(100)
                            ->suffix('%'),
                        Forms\Components\TextInput::make('magenta_toner_level')
                            ->label('الحبر الوردي (Magenta %)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(100)
                            ->suffix('%'),
                        Forms\Components\TextInput::make('yellow_toner_level')
                            ->label('الحبر الأصفر (Yellow %)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(100)
                            ->suffix('%'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('machine_code')
                    ->label('كود الماكينة')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('name.ar')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch.name.ar')
                    ->label('الفرع')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'online' => 'success',
                        'busy' => 'warning',
                        'maintenance' => 'danger',
                        'offline' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('paper_tray_a4_sheets')
                    ->label('ورق A4 المتبقي')
                    ->badge()
                    ->color(fn ($state) => $state < 50 ? 'danger' : ($state < 150 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('black_toner_level')
                    ->label('الحبر الأسود')
                    ->suffix('%')
                    ->badge()
                    ->color(fn ($state) => $state < 15 ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('last_ping_at')
                    ->label('آخر اتصال')
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة الماكينة')
                    ->options([
                        'online' => 'Online',
                        'offline' => 'Offline',
                        'maintenance' => 'Maintenance',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('refill_paper')
                    ->label('إعادة تعبئة الورق')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (KioskMachine $record) => $record->update([
                        'paper_tray_a4_sheets' => 500,
                        'is_paper_low' => false,
                        'is_paper_empty' => false,
                    ])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKioskMachines::route('/'),
            'create' => Pages\CreateKioskMachine::route('/create'),
            'edit' => Pages\EditKioskMachine::route('/{record}/edit'),
        ];
    }
}
