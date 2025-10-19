<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransaksiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
			'customer_id' => ['required'],
			'tanggal' => ['required', 'date'],
			'nominal' => ['required', 'string'],
			'kategori_id' => ['required'],
			'keterangan' => ['required', 'string'],
        ];
    }
}
