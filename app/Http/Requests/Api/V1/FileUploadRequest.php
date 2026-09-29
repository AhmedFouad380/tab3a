<?php

namespace App\Http\Requests\Api\V1;

class FileUploadRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:51200',
        ];
    }

    public function messages(): array
    {
        return [
            'file.max' => $this->getLocale() === 'en' ? 'The file may not be greater than 50MB.' : 'حجم الملف يجب ألا يتجاوز 50 ميجابايت.',
            'file.mimes' => $this->getLocale() === 'en' ? 'Supported file types are PDF, JPG, PNG.' : 'الملفات المدعومة هي PDF و JPG و PNG فقط.',
        ];
    }
}
