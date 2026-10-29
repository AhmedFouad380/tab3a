<?php

namespace App\Filament\Resources\BranchResource\Pages;

use App\Filament\Resources\BranchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranch extends EditRecord
{
    protected static string $resource = BranchResource::class;

    protected array $schedules = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $schedules = $this->record->getWorkingHoursSchedules();

        $data['working_hours_schedules'] = ! empty($schedules) ? $schedules : [
            [
                'days' => ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'],
                'opening_time' => '08:00',
                'closing_time' => '22:00',
                'is_day_off' => false,
            ],
        ];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->schedules = $data['working_hours_schedules'] ?? [];
        unset($data['working_hours_schedules']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->syncWorkingHours($this->schedules);

        $this->data['working_hours_schedules'] = $this->record->fresh()->getWorkingHoursSchedules();
    }
}
