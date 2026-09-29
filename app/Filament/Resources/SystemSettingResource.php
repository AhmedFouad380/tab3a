<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemSettingResource\Pages;
use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = '⚙️ الإعدادات العامة';
    protected static ?string $modelLabel = 'إعداد';
    protected static ?string $pluralModelLabel = 'إعدادات النظام والتطبيق';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل الإعداد')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('مفتاح الإعداد (Key)')
                            ->required()
                            ->disabled(fn (string $context): bool => $context === 'edit')
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('group')
                            ->label('المجموعة')
                            ->options([
                                'general' => 'إعدادات عامة والهوية',
                                'pricing' => 'الضرائب والمالية',
                                'print_settings' => 'إعدادات وقيود الطباعة',
                                'contact' => 'بيانات التواصل الرسمية',
                            ])
                            ->default('general')
                            ->required(),
                        Forms\Components\TextInput::make('display_name.ar')
                            ->label('اسم الإعداد بالعربي')
                            ->required(),
                        Forms\Components\TextInput::make('display_name.en')
                            ->label('Setting Name (English)')
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->label('نوع الحقل')
                            ->options([
                                'string' => 'نص قصير',
                                'text' => 'نص طويل',
                                'image' => 'صورة / لوجو',
                            ])
                            ->default('string')
                            ->reactive()
                            ->required(),
                        Forms\Components\FileUpload::make('value.ar')
                            ->label('رفع الصورة / اللوجو')
                            ->image()
                            ->directory('settings')
                            ->visibility('public')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'image' || $get('key') === 'app_logo')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('value.ar')
                            ->label('القيمة (عربي / القيمة العامة)')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('type') !== 'image' && $get('key') !== 'app_logo')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('value.en')
                            ->label('القيمة بالإنجليزية (اختياري)')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('type') !== 'image' && $get('key') !== 'app_logo')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name.ar')
                    ->label('اسم الإعداد')
                    ->searchable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('المفتاح')
                    ->badge(),
                Tables\Columns\TextColumn::make('group')
                    ->label('المجموعة')
                    ->badge(),
                Tables\Columns\TextColumn::make('value.ar')
                    ->label('القيمة')
                    ->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'general' => 'عام',
                        'pricing' => 'الضرائب والمالية',
                        'print_settings' => 'إعدادات الطباعة',
                        'contact' => 'التواصل',
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
            'index' => Pages\ListSystemSettings::route('/'),
            'create' => Pages\CreateSystemSetting::route('/create'),
            'edit' => Pages\EditSystemSetting::route('/{record}/edit'),
        ];
    }
}
