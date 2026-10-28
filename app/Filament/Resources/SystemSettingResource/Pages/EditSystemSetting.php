<?php

namespace App\Filament\Resources\SystemSettingResource\Pages;

use App\Filament\Resources\SystemSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSystemSetting extends EditRecord
{
    protected static string $resource = SystemSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (($data['type'] ?? '') === 'image' || ($data['key'] ?? '') === 'app_logo') {
            $img = $data['value']['ar'] ?? $data['value'] ?? null;
            $data['image_file'] = is_array($img) ? reset($img) : $img;
        }

        if (isset($data['value']) && is_array($data['value'])) {
            if (isset($data['value']['ar']) && is_array($data['value']['ar'])) {
                $data['value']['ar'] = reset($data['value']['ar']);
            }
            if (isset($data['value']['en']) && is_array($data['value']['en'])) {
                $data['value']['en'] = reset($data['value']['en']);
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['type'] ?? '') === 'image' || ($data['key'] ?? '') === 'app_logo') {
            $uploaded = $data['image_file'] ?? null;
            $imgPath = is_array($uploaded) ? reset($uploaded) : $uploaded;
            $data['value'] = [
                'ar' => $imgPath,
                'en' => $imgPath,
            ];
        } else {
            if (isset($data['value']) && is_array($data['value'])) {
                if (isset($data['value']['ar']) && is_array($data['value']['ar'])) {
                    $data['value']['ar'] = reset($data['value']['ar']);
                }
                if (isset($data['value']['en']) && is_array($data['value']['en'])) {
                    $data['value']['en'] = reset($data['value']['en']);
                }
            }
        }

        return $data;
    }
}
