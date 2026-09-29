<?php

namespace App\Filament\Resources\FinishingOptionResource\Pages;

use App\Filament\Resources\FinishingOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinishingOption extends EditRecord
{
    protected static string $resource = FinishingOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
