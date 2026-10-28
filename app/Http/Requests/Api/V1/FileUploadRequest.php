<?php

namespace App\Http\Requests\Api\V1;

use App\Models\KioskMachine;
use App\Models\KioskSession;
use App\Models\SystemSetting;

class FileUploadRequest extends BaseApiRequest
{
    /**
     * Determine the effective max file size in MB.
     */
    protected function resolveMaxFileSizeMb(): int
    {
        if ($this->filled('kiosk_machine_id')) {
            $kiosk = KioskMachine::find($this->kiosk_machine_id);
            if ($kiosk) {
                return $kiosk->getEffectiveMaxFileSizeMb();
            }
        }

        if ($this->filled('session_token')) {
            $session = KioskSession::where('session_token', $this->session_token)->first();
            if ($session?->kiosk) {
                return $session->kiosk->getEffectiveMaxFileSizeMb();
            }
        }

        $globalSetting = SystemSetting::getSetting('max_file_size_mb', 'ar');
        if (! $globalSetting) {
            $globalSetting = SystemSetting::getSetting('instant_print_max_file_size', 'ar', 50);
        }

        return (int) $globalSetting;
    }

    public function rules(): array
    {
        $maxMb = $this->resolveMaxFileSizeMb();
        $maxKb = $maxMb * 1024;

        return [
            'file' => "required|file|mimes:pdf,jpg,jpeg,png|max:{$maxKb}",
            'kiosk_machine_id' => 'nullable|exists:kiosk_machines,id',
            'session_token' => 'nullable|string',
            'copies' => 'nullable|integer|min:1|max:50',
            'color_mode' => 'nullable|in:black_and_white,color',
            'side_mode' => 'nullable|in:single_sided,double_sided',
        ];
    }

    public function messages(): array
    {
        $maxMb = $this->resolveMaxFileSizeMb();

        return [
            'file.max' => $this->getLocale() === 'en'
                ? "The file may not be greater than {$maxMb}MB."
                : "حجم الملف يجب ألا يتجاوز {$maxMb} ميجابايت.",
            'file.mimes' => $this->getLocale() === 'en'
                ? 'Supported file types are PDF, JPG, PNG.'
                : 'الملفات المدعومة هي PDF و JPG و PNG فقط.',
        ];
    }
}
