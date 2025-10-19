<?php

namespace App\Http\Resources\Transaksi;

use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
			'customer' => $this->customer->nama,
            'customer_id' => $this->customer_id,
            'tanggal' => date('d-m-Y', strtotime($this->tanggal)),
			'nominal' => $this->toRupiah($this->nominal),
			'kategori' => $this->kategori->nama_kategory,
            'kategori_id' => $this->kategori_id,
            'point' => $this->point ?? 0 ,
			'keterangan' => $this->keterangan,
            // 'created_at' => dateTimeFormat($this->created_at), date format
            // 'updated_at' => dateTimeFormat($this->updated_at),
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
            'updated_at' => date('d-m-Y', strtotime($this->updated_at)),

        ];
    }

    function  toRupiah($angka)
    {
        return "Rp. " . number_format($angka, 0, ',', '.');
    }
}
