<?php

namespace App\Http\Requests\Cabang;

use Illuminate\Foundation\Http\FormRequest;

class CreateCabangRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama_cabang' => ['required', 'string'],
			'code_cabang' => ['nullable', 'string'],
			'alamat' => ['nullable', 'string'],
			'nohp' => ['nullable', 'string'],
        ];
    }
}
