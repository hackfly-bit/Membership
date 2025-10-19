<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransaksiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['sometimes', 'string'],
			'customer_id' => ['sometimes'],
			'tanggal' => ['sometimes', 'date'],
			'nominal' => ['sometimes', 'string'],
			'kategori_id' => ['sometimes'],
			'keterangan' => ['sometimes', 'string'],
        ];
    }
}
