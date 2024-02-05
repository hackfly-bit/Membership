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
            'tanggal' => date('d-m-Y', strtotime($this->tanggal)),
			'nominal' => $this->toRupiah($this->nominal),
			'kategori' => $this->kategori->nama_kategory,
            'point' => $this->kategori->point,
			'keterangan' => $this->keterangan,
            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
        ];
    }

    function  toRupiah($angka)
    {
        return "Rp. " . number_format($angka, 0, ',', '.');
    }
}
