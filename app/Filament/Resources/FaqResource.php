<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = '📝 المحتوى وخدمة العملاء';
    protected static ?string $modelLabel = 'سؤال شائع';
    protected static ?string $pluralModelLabel = 'الأسئلة الشائعة (FAQs)';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('السؤال والإجابة')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->label('التصنيف')
                            ->options([
                                'general' => 'عام (General)',
                                'self_printing' => 'الطباعة الذاتية',
                                'pre_order' => 'الطباعة المسبقة والفروع',
                                'payment' => 'الدفع والمحفظة',
                            ])
                            ->default('general')
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتيب العرض')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('question.ar')
                            ->label('السؤال (بالعربية)')
                            ->required(),
                        Forms\Components\TextInput::make('question.en')
                            ->label('Question (English)')
                            ->required(),
                        Forms\Components\Textarea::make('answer.ar')
                            ->label('الإجابة (بالعربية)')
                            ->rows(3)
                            ->required(),
                        Forms\Components\Textarea::make('answer.en')
                            ->label('Answer (English)')
                            ->rows(3)
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
                Tables\Columns\TextColumn::make('question.ar')
                    ->label('السؤال')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('التصنيف')
                    ->badge(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),
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
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
