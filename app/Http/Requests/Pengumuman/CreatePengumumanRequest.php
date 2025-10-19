<?php

namespace App\Http\Requests\Pengumuman;

use Illuminate\Foundation\Http\FormRequest;

class CreatePengumumanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'judul' => ['nullable', 'string'],
			'isi' => ['nullable', 'string'],
        ];
    }
}
