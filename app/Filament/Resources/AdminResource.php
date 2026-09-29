<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Models\Admin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = '👥 المستخدمين وفريق العمل';
    protected static ?string $modelLabel = 'مشرف / موظف';
    protected static ?string $pluralModelLabel = 'المدراء وفريق العمل';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الحساب')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم الكامل')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel(),
                        Forms\Components\Select::make('role')
                            ->label('الدور / الصلاحية')
                            ->options([
                                'super_admin' => 'مدير عام (Super Admin)',
                                'admin' => 'مشرف نظام (Admin)',
                                'branch_manager' => 'مدير / موظف فرع (Branch Staff)',
                                'support_agent' => 'خدمة عملاء (Support)',
                            ])
                            ->default('admin')
                            ->required(),
                        Forms\Components\Select::make('branch_id')
                            ->label('الفرع التابع له (لموظفي الفروع)')
                            ->relationship('branch', 'name->ar')
                            ->searchable(),
                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('الحساب نشط')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('الدور')
                    ->badge(),
                Tables\Columns\TextColumn::make('branch.name.ar')
                    ->label('الفرع')
                    ->placeholder('-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('الدور')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin',
                        'branch_manager' => 'Branch Manager',
                        'support_agent' => 'Support Agent',
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
