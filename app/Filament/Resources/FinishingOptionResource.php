<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinishingOptionResource\Pages;
use App\Models\FinishingOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FinishingOptionResource extends Resource
{
    protected static ?string $model = FinishingOption::class;

    protected static ?string $navigationIcon = 'heroicon-o-scissors';
    protected static ?string $navigationGroup = '🏢 الفروع والتسعير';
    protected static ?string $modelLabel = 'خيار تجليد/تشطيب';
    protected static ?string $pluralModelLabel = 'خيارات التجليد والتشطيب';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات خيار التجليد')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name.ar')
                            ->label('الاسم (بالعربية)')
                            ->required(),
                        Forms\Components\TextInput::make('name.en')
                            ->label('Name (English)')
                            ->required(),
                        Forms\Components\TextInput::make('code')
                            ->label('رمز الكود (Unique Code)')
                            ->placeholder('e.g. spiral_binding')
                            ->required(),
                        Forms\Components\TextInput::make('base_price')
                            ->label('السعر الإضافي')
                            ->numeric()
                            ->prefix('SAR')
                            ->required(),
                        Forms\Components\Textarea::make('description.ar')
                            ->label('الوصف بالعربي')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description.en')
                            ->label('Description in English')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('available_for_pre_order')
                            ->label('متاح في الطباعة المسبقة بالفروع')
                            ->default(true),
                        Forms\Components\Toggle::make('available_for_self_print')
                            ->label('متاح في الماكينات الذكية')
                            ->default(false),
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
                Tables\Columns\TextColumn::make('name.ar')
                    ->label('اسم الخيار')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->badge(),
                Tables\Columns\TextColumn::make('base_price')
                    ->label('السعر')
                    ->money('SAR'),
                Tables\Columns\IconColumn::make('available_for_pre_order')
                    ->label('متاح بالفروع')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('مفعل')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinishingOptions::route('/'),
            'create' => Pages\CreateFinishingOption::route('/create'),
            'edit' => Pages\EditFinishingOption::route('/{record}/edit'),
        ];
    }
}
