<?php

namespace App\Http\Requests\Pengumuman;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengumumanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'judul' => ['sometimes', 'string'],
			'isi' => ['sometimes', 'string'],
        ];
    }
}
