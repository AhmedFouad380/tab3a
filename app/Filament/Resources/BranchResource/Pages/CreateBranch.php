<?php

namespace App\Filament\Resources\BranchResource\Pages;

use App\Filament\Resources\BranchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBranch extends CreateRecord
{
    protected static string $resource = BranchResource::class;

    protected array $schedules = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->schedules = $data['working_hours_schedules'] ?? [];
        unset($data['working_hours_schedules']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncWorkingHours($this->schedules);
    }
}
