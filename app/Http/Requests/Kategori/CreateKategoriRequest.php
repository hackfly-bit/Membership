<?php

namespace App\Http\Requests\Kategori;

use Illuminate\Foundation\Http\FormRequest;

class CreateKategoriRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama_kategory' => ['required', 'string'],
			'point' => ['required', 'integer'],
        ];
    }
}
