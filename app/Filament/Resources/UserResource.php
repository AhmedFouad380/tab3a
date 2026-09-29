<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = '👥 المستخدمين وفريق العمل';
    protected static ?string $modelLabel = 'عميل';
    protected static ?string $pluralModelLabel = 'العملاء (المستخدمين)';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات العميل والمحفظة')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الجوال')
                            ->tel()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email(),
                        Forms\Components\Select::make('preferred_locale')
                            ->label('اللغة المفضلة')
                            ->options([
                                'ar' => 'العربية',
                                'en' => 'English',
                            ])
                            ->default('ar'),
                        Forms\Components\TextInput::make('wallet_balance')
                            ->label('رصيد المحفظة')
                            ->numeric()
                            ->prefix('SAR')
                            ->default(0.00),
                        Forms\Components\Select::make('status')
                            ->label('حالة الحساب')
                            ->options([
                                'active' => 'نشط (Active)',
                                'blocked' => 'محظور (Blocked)',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->placeholder('مستخدم جديد'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الجوال')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('wallet_balance')
                    ->label('رصيد المحفظة')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('عدد الطلبات')
                    ->counts('orders')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'blocked' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'نشط',
                        'blocked' => 'محظور',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
