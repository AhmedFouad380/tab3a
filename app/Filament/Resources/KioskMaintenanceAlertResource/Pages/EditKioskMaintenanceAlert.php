<?php

namespace App\Filament\Resources\KioskMaintenanceAlertResource\Pages;

use App\Filament\Resources\KioskMaintenanceAlertResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKioskMaintenanceAlert extends EditRecord
{
    protected static string $resource = KioskMaintenanceAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
