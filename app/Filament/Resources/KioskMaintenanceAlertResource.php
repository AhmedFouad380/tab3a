<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KioskMaintenanceAlertResource\Pages;
use App\Models\KioskMaintenanceAlert;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KioskMaintenanceAlertResource extends Resource
{
    protected static ?string $model = KioskMaintenanceAlert::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = '🖨️ إدارة العمليات والطباعة';
    protected static ?string $modelLabel = 'تنبيه صيانة ماكينة';
    protected static ?string $pluralModelLabel = 'تنبيهات الماكينات والأعطال';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kiosk_machine_id')
                    ->label('الماكينة')
                    ->relationship('kiosk', 'machine_code')
                    ->required(),
                Forms\Components\Select::make('alert_type')
                    ->label('نوع التنبيه')
                    ->options([
                        'out_of_paper' => 'نفاد الورق تماماً',
                        'low_paper' => 'رصيد الورق منخفض',
                        'low_toner' => 'مستوى الحبر منخفض',
                        'paper_jam' => 'انحشار ورق (Paper Jam)',
                        'door_open' => 'باب الماكينة مفتوح',
                        'offline' => 'انقطاع الاتصال',
                    ])
                    ->required(),
                Forms\Components\Select::make('severity')
                    ->label('مستوى الخطورة')
                    ->options([
                        'info' => 'معلومة (Info)',
                        'warning' => 'تحذير (Warning)',
                        'critical' => 'حرج (Critical)',
                    ])
                    ->default('warning')
                    ->required(),
                Forms\Components\TextInput::make('message.ar')
                    ->label('رسالة التنبيه (عربي)')
                    ->required(),
                Forms\Components\TextInput::make('message.en')
                    ->label('Alert Message (English)')
                    ->required(),
                Forms\Components\Toggle::make('is_resolved')
                    ->label('تم الحل والمعالجة')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kiosk.machine_code')
                    ->label('كود الماكينة')
                    ->badge(),
                Tables\Columns\TextColumn::make('alert_type')
                    ->label('نوع التنبيه')
                    ->badge(),
                Tables\Columns\TextColumn::make('severity')
                    ->label('الخطورة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'warning' => 'warning',
                        'info' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('message.ar')
                    ->label('الرسالة'),
                Tables\Columns\IconColumn::make('is_resolved')
                    ->label('تم الحل')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('وقت التنبيه')
                    ->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_resolved')
                    ->label('حالة المعالجة'),
            ])
            ->actions([
                Tables\Actions\Action::make('resolve')
                    ->label('تمت الصيانة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (KioskMaintenanceAlert $record) => ! $record->is_resolved)
                    ->action(fn (KioskMaintenanceAlert $record) => $record->update([
                        'is_resolved' => true,
                        'resolved_at' => now(),
                    ])),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKioskMaintenanceAlerts::route('/'),
            'create' => Pages\CreateKioskMaintenanceAlert::route('/create'),
            'edit' => Pages\EditKioskMaintenanceAlert::route('/{record}/edit'),
        ];
    }
}
