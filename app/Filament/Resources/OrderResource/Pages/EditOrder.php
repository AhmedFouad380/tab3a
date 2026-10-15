<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_file')
                ->label(fn () => $this->record->items()->count() > 1 ? 'تنزيل الملفات (ZIP)' : 'تنزيل الملف المرفق')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn () => route('admin.orders.download', $this->record))
                ->visible(fn () => $this->record->items()->exists()),
            Actions\Action::make('preview_file')
                ->label('معاينة الملف')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => $this->record->items()->first() ? route('admin.orders.items.preview', $this->record->items()->first()) : null)
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->items()->exists()),
            Actions\DeleteAction::make(),
        ];
    }
}
