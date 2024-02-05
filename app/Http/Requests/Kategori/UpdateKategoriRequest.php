<?php

namespace App\Http\Requests\Kategori;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKategoriRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama_kategory' => ['sometimes', 'string'],
			'point' => ['sometimes', 'integer'],
        ];
    }
}
