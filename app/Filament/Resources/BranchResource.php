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
