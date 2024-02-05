<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string'],
			'nohp' => ['nullable', 'string'],
			'email' => ['nullable', 'email', 'string'],
			'role' => ['nullable', 'string'],
			'cabang_id' => ['required'],
        ];
    }
}
