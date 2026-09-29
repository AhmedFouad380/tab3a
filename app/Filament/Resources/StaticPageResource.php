<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaticPageResource\Pages;
use App\Models\StaticPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StaticPageResource extends Resource
{
    protected static ?string $model = StaticPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = '📝 المحتوى وخدمة العملاء';
    protected static ?string $modelLabel = 'صفحة تعريفية';
    protected static ?string $pluralModelLabel = 'الصفحات التعريفية (CMS)';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات ومحتوى الصفحة')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->label('المعرف اللطيف (Slug)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('about-us / privacy-policy / terms'),
                        Forms\Components\Toggle::make('is_published')
                            ->label('منشورة ومتاحة بالتطبيق')
                            ->default(true),
                        Forms\Components\TextInput::make('title.ar')
                            ->label('عنوان الصفحة (بالعربية)')
                            ->required(),
                        Forms\Components\TextInput::make('title.en')
                            ->label('Page Title (English)')
                            ->required(),
                        Forms\Components\RichEditor::make('content.ar')
                            ->label('محتوى الصفحة (بالعربية)')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content.en')
                            ->label('Page Content (English)')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title.ar')
                    ->label('عنوان الصفحة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('الـ Slug')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('منشورة')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تعديل')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaticPages::route('/'),
            'create' => Pages\CreateStaticPage::route('/create'),
            'edit' => Pages\EditStaticPage::route('/{record}/edit'),
        ];
    }
}
