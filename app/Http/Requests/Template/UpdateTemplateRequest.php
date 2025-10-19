<?php

namespace App\Http\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'judul' => ['sometimes', 'string'],
			'message' => ['sometimes', 'string'],
            'setting_template' => ['sometimes','string'],
        ];
    }
}
