<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string'],
            'role' => ['required', 'string'],
            'password' => ['required', 'string'],
            'cabang_id' => ['required', 'integer'],
            'token_wa' => ['required', 'string'],

        ];
    }
}
