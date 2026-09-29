<?php

namespace App\Http\Requests\Api\V1;

class ContactMessageRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
        ];
    }
}
