<?php

namespace App\Http\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;

class CreateTemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string'],
			'message' => ['required', 'string'],
        ];
    }
}
