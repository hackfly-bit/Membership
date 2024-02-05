<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama' => ['sometimes', 'string'],
			'nohp' => ['sometimes', 'string'],
			'email' => ['sometimes', 'email', 'string'],
			'role' => ['sometimes', 'string'],
			'cabang_id' => ['sometimes'],
        ];
    }
}
