<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = '👥 المستخدمين وفريق العمل';
    protected static ?string $modelLabel = 'دور وصلاحية';
    protected static ?string $pluralModelLabel = 'الأدوار والصلاحيات';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $permissionLabels = [
            'view_orders' => 'عرض طلبات الطباعة',
            'manage_orders' => 'إدارة وتحديث حالات الطلبات',
            'download_order_files' => 'تنزيل ومعاينة ملفات الطلبات',
            'manage_branches' => 'إدارة الفروع ومواعيد العمل',
            'manage_kiosks' => 'إدارة ومراقبة ماكينات الطباعة الذاتية',
            'manage_pricing' => 'إدارة قواعد التسعير وخيارات التشطيب',
            'manage_users' => 'إدارة حسابات ومحافظ العملاء',
            'manage_admins' => 'إدارة حسابات المدراء وفريق العمل',
            'manage_roles' => 'إدارة الأدوار والصلاحيات',
            'manage_settings' => 'إدارة إعدادات النظام والصفحات الثابتة',
        ];

        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الدور')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الدور (الرمز)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('e.g. branch_supervisor'),
                        Forms\Components\TextInput::make('guard_name')
                            ->label('الحارس (Guard)')
                            ->default('admin')
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ]),

                Forms\Components\Section::make('الصلاحيات الممنوحة لهذا الدور')
                    ->description('حدد الصلاحيات التي يمتلكها المشرفون المسند إليهم هذا الدور')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('قائمة الصلاحيات')
                            ->relationship(
                                name: 'permissions',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->where('guard_name', 'admin')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => ($permissionLabels[$record->name] ?? $record->name) . " ({$record->name})")
                            ->columns(2)
                            ->bulkToggleable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $roleLabels = [
            'super_admin' => 'مدير عام (Super Admin)',
            'admin' => 'مشرف نظام (Admin)',
            'branch_manager' => 'مدير / موظف فرع (Branch Manager)',
            'support_agent' => 'خدمة عملاء (Support)',
        ];

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الدور')
                    ->formatStateUsing(fn (string $state) => $roleLabels[$state] ?? $state)
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('الحارس')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('عدد الصلاحيات')
                    ->counts('permissions')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('المشرفون المسند إليهم')
                    ->counts('users')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Role $record) => in_array($record->name, ['super_admin', 'admin'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
