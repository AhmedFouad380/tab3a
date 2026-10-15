<?php

namespace App\Http\Requests\Api\V1;

class CompleteProfileRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'preferred_locale' => 'nullable|in:ar,en',
            'is_company' => 'nullable|boolean',
            'tax_number' => 'required_if:is_company,true,1|nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        $isAr = $this->getLocale() === 'ar';

        return [
            'tax_number.required_if' => $isAr
                ? 'الرقم الضريبي مطلوب عند التسجيل كشركة'
                : 'Tax number is required when registering as a company.',
        ];
    }
}
