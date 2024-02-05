<?php

namespace App\Http\Requests\Cabang;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCabangRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama_cabang' => ['sometimes', 'string'],
			'code_cabang' => ['sometimes', 'string'],
			'alamat' => ['sometimes', 'string'],
			'nohp' => ['sometimes', 'string'],
        ];
    }
}
