<?php

namespace App\Filament\Resources\KioskMachineResource\Pages;

use App\Filament\Resources\KioskMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKioskMachine extends EditRecord
{
    protected static string $resource = KioskMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
