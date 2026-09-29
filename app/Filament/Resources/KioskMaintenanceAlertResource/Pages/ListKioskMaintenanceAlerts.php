<?php

namespace App\Filament\Resources\KioskMaintenanceAlertResource\Pages;

use App\Filament\Resources\KioskMaintenanceAlertResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKioskMaintenanceAlerts extends ListRecords
{
    protected static string $resource = KioskMaintenanceAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
