<?php

namespace App\Http\Requests\Api\V1;

use App\Models\SystemSetting;

class FileUploadRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $maxMb = (int) (SystemSetting::where('key', 'instant_print_max_file_size')->first()?->value['ar'] ?? 30);
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
        $maxMb = (int) (SystemSetting::where('key', 'instant_print_max_file_size')->first()?->value['ar'] ?? 30);

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
