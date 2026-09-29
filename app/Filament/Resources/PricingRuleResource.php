<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PricingRuleResource\Pages;
use App\Models\PricingRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PricingRuleResource extends Resource
{
    protected static ?string $model = PricingRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = '🏢 الفروع والتسعير';
    protected static ?string $modelLabel = 'قاعدة تسعير';
    protected static ?string $pluralModelLabel = 'قواعد تسعير الطباعة';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('مواصفات التسعير')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('service_type')
                            ->label('نوع الخدمة المطبقة عليها')
                            ->options([
                                'all' => 'كافة الخدمات (الذاتية والمسبقة)',
                                'self_printing' => 'الطباعة الذاتية فقط',
                                'pre_order' => 'الطباعة المسبقة فقط',
                            ])
                            ->default('all')
                            ->required(),

                        Forms\Components\Select::make('paper_size')
                            ->label('مقاس الورق')
                            ->options([
                                'A4' => 'A4 (القياسي)',
                                'A3' => 'A3 (الكبير)',
                                'A5' => 'A5',
                            ])
                            ->default('A4')
                            ->required(),

                        Forms\Components\Select::make('color_mode')
                            ->label('نوع الألوان')
                            ->options([
                                'black_and_white' => 'أبيض وأسود (Black & White)',
                                'color' => 'ألوان (Full Color)',
                            ])
                            ->required(),

                        Forms\Components\Select::make('side_mode')
                            ->label('طريقة الطباعة')
                            ->options([
                                'single_sided' => 'وجه واحد (Single-sided)',
                                'double_sided' => 'وجهين (Double-sided)',
                            ])
                            ->required(),

                        Forms\Components\Select::make('paper_type')
                            ->label('نوع وسمك الورق')
                            ->options([
                                'plain_80g' => 'ورق عادي 80 جرام',
                                'glossy_150g' => 'ورق كوشيه لامع 150 جرام',
                                'cardstock_300g' => 'ورق مقوى كرتون 300 جرام',
                            ])
                            ->default('plain_80g')
                            ->required(),

                        Forms\Components\TextInput::make('price_per_page')
                            ->label('سعر الصفحة')
                            ->numeric()
                            ->prefix('SAR')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('مفعل')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paper_size')
                    ->label('مقاس الورق')
                    ->badge(),
                Tables\Columns\TextColumn::make('color_mode')
                    ->label('الألوان')
                    ->formatStateUsing(fn (string $state) => $state === 'color' ? '🎨 ألوان' : '⬛ أبيض وأسود')
                    ->badge()
                    ->color(fn (string $state) => $state === 'color' ? 'warning' : 'gray'),
                Tables\Columns\TextColumn::make('side_mode')
                    ->label('الوجه')
                    ->formatStateUsing(fn (string $state) => $state === 'double_sided' ? 'وجهين' : 'وجه واحد'),
                Tables\Columns\TextColumn::make('paper_type')
                    ->label('نوع الورق'),
                Tables\Columns\TextColumn::make('price_per_page')
                    ->label('سعر الصفحة')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعل')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('color_mode')
                    ->label('الألوان')
                    ->options([
                        'black_and_white' => 'أبيض وأسود',
                        'color' => 'ألوان',
                    ]),
                Tables\Filters\SelectFilter::make('paper_size')
                    ->label('المقاس')
                    ->options([
                        'A4' => 'A4',
                        'A3' => 'A3',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPricingRules::route('/'),
            'create' => Pages\CreatePricingRule::route('/create'),
            'edit' => Pages\EditPricingRule::route('/{record}/edit'),
        ];
    }
}
