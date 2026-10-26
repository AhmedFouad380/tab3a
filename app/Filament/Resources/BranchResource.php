<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = '🏢 الفروع والتسعير';

    protected static ?string $modelLabel = 'فرع';

    protected static ?string $pluralModelLabel = 'الفروع';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الفرع الأساسية')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name.ar')
                            ->label('اسم الفرع (بالعربية)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name.en')
                            ->label('Branch Name (English)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label('المدينة')
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel(),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('رقم الواتساب')
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email(),
                    ]),

                Forms\Components\Section::make('الموقع الجغرافي والعنوان')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('address.ar')
                            ->label('العنوان التفصيلي (عربي)')
                            ->rows(2)
                            ->required(),
                        Forms\Components\Textarea::make('address.en')
                            ->label('Detailed Address (English)')
                            ->rows(2)
                            ->required(),
                        Forms\Components\TextInput::make('latitude')
                            ->label('خط العرض (Latitude)')
                            ->numeric(),
                        Forms\Components\TextInput::make('longitude')
                            ->label('خط الطول (Longitude)')
                            ->numeric(),
                    ]),

                Forms\Components\Section::make('الإعدادات والطاقة الاستيعابية')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('daily_capacity')
                            ->label('الطاقة الاستيعابية اليومية (طلبات)')
                            ->numeric()
                            ->default(100),
                        Forms\Components\Toggle::make('allows_pre_order')
                            ->label('تفعيل استقبال الطباعة المسبقة')
                            ->default(true),
                        Forms\Components\Toggle::make('is_active')
                            ->label('الفرع نشط ويعمل')
                            ->default(true),
                    ]),

                Forms\Components\Section::make('⏰ مواعيد وساعات العمل')
                    ->description('حدد فترات ومواعيد العمل (من وإلى) مع اختيار أيام الأسبوع المناسبة لكل فترة')
                    ->schema([
                        Forms\Components\Repeater::make('working_hours_schedules')
                            ->label('فترات ومواعيد العمل')
                            ->schema([
                                Forms\Components\CheckboxList::make('days')
                                    ->label('أيام الأسبوع')
                                    ->options([
                                        'sunday' => 'الأحد (Sunday)',
                                        'monday' => 'الإثنين (Monday)',
                                        'tuesday' => 'الثلاثاء (Tuesday)',
                                        'wednesday' => 'الأربعاء (Wednesday)',
                                        'thursday' => 'الخميس (Thursday)',
                                        'friday' => 'الجمعة (Friday)',
                                        'saturday' => 'السبت (Saturday)',
                                    ])
                                    ->columns(4)
                                    ->bulkToggleable()
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\TimePicker::make('opening_time')
                                    ->label('وقت بدء العمل (من)')
                                    ->seconds(false)
                                    ->default('08:00')
                                    ->required(fn (Forms\Get $get) => ! $get('is_day_off'))
                                    ->visible(fn (Forms\Get $get) => ! $get('is_day_off')),

                                Forms\Components\TimePicker::make('closing_time')
                                    ->label('وقت انتهاء العمل (إلى)')
                                    ->seconds(false)
                                    ->default('22:00')
                                    ->required(fn (Forms\Get $get) => ! $get('is_day_off'))
                                    ->visible(fn (Forms\Get $get) => ! $get('is_day_off')),

                                Forms\Components\Toggle::make('is_day_off')
                                    ->label('عطلة / مغلق في هذه الأيام')
                                    ->default(false)
                                    ->live(),
                            ])
                            ->columns(3)
                            ->default([
                                [
                                    'days' => ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'],
                                    'opening_time' => '08:00',
                                    'closing_time' => '22:00',
                                    'is_day_off' => false,
                                ],
                            ])
                            ->addActionLabel('+ إضافة فترة عمل أخرى')
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                $days = $state['days'] ?? [];
                                if (empty($days)) {
                                    return 'فترة عمل جديدة';
                                }

                                $daysMap = [
                                    'sunday' => 'الأحد',
                                    'monday' => 'الإثنين',
                                    'tuesday' => 'الثلاثاء',
                                    'wednesday' => 'الأربعاء',
                                    'thursday' => 'الخميس',
                                    'friday' => 'الجمعة',
                                    'saturday' => 'السبت',
                                ];

                                $dayNames = array_map(fn ($d) => $daysMap[$d] ?? $d, $days);
                                $daysStr = implode('، ', $dayNames);

                                if (! empty($state['is_day_off'])) {
                                    return "🚫 عطلة رسمية: {$daysStr}";
                                }

                                $from = ! empty($state['opening_time']) ? substr($state['opening_time'], 0, 5) : '--:--';
                                $to = ! empty($state['closing_time']) ? substr($state['closing_time'], 0, 5) : '--:--';

                                return "🕒 من {$from} إلى {$to} ({$daysStr})";
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name.ar')
                    ->label('اسم الفرع')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('المدينة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف'),
                Tables\Columns\TextColumn::make('working_hours_summary')
                    ->label('مواعيد العمل')
                    ->badge()
                    ->color('info')
                    ->tooltip(fn (Branch $record) => $record->working_hours_summary),
                Tables\Columns\IconColumn::make('is_open')
                    ->label('مفتوح الآن')
                    ->state(fn (Branch $record) => $record->isOpenNow())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\IconColumn::make('allows_pre_order')
                    ->label('طباعة مسبقة')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                Tables\Columns\TextColumn::make('kiosks_count')
                    ->label('عدد الماكينات')
                    ->counts('kiosks')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('الحالة'),
                Tables\Filters\TernaryFilter::make('allows_pre_order')->label('يدعم الطباعة المسبقة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
