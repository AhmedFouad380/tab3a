<?php

namespace App\Filament\Resources\KioskMachineResource\Pages;

use App\Filament\Resources\KioskMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKioskMachines extends ListRecords
{
    protected static string $resource = KioskMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
