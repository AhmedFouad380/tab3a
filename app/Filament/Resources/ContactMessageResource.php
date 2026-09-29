<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = '📝 المحتوى وخدمة العملاء';
    protected static ?string $modelLabel = 'رسالة تواصل';
    protected static ?string $pluralModelLabel = 'رسائل اتصل بنا';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل الرسالة والرد')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->disabled(),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->disabled(),
                        Forms\Components\TextInput::make('subject')
                            ->label('الموضوع')
                            ->disabled(),
                        Forms\Components\Textarea::make('message')
                            ->label('نص الرسالة')
                            ->rows(3)
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label('حالة الرسالة')
                            ->options([
                                'new' => 'جديدة (New)',
                                'read' => 'تمت القراءة (Read)',
                                'in_progress' => 'قيد المتابعة (In Progress)',
                                'resolved' => 'تم الحل (Resolved)',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('admin_reply')
                            ->label('رد الإدارة')
                            ->rows(3)
                            ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('الموضوع')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'resolved' => 'success',
                        'in_progress' => 'warning',
                        'read' => 'info',
                        'new' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'جديدة',
                        'in_progress' => 'قيد المتابعة',
                        'resolved' => 'تم الحل',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('معاينة والرد'),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
