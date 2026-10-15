<?php

namespace App\Http\Requests\Api\V1;

class RegisterRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'phone_country_code' => 'nullable|string|max:10',
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
            'name.required' => $isAr
                ? 'يرجى إدخال الاسم'
                : 'Name is required.',
            'phone.required' => $isAr
                ? 'يرجى إدخال رقم الجوال'
                : 'Phone number is required.',
        ];
    }
}
