<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
			'nohp' => $this->nohp,
			'email' => $this->email,
			'role' => $this->role,
			'cabang' => $this->cabang->nama_cabang ?? null,
            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
            'nominal_belanja' => $this->countPointTransaksi($this->transaksi),
            // get point from relational transaksi with category
            'point' => $this->getPoint($this->transaksi),
            'transaksi_data' => $this->transaksi,
        ];
    }

    function countPointTransaksi($transaksi)
    {
        if (empty($transaksi)) {
            return "Belum Bertransaksi";
        }

        $total = 0;
        foreach ($transaksi as $item) {
            $total += $item->nominal;
        }
        return "Rp. " . number_format($total, 0, ',', '.');
    }

    function getPoint($transaksi)
    {
        if (empty($transaksi)) {
            return 0;
        }

        $total = 0;
        foreach ($transaksi as $item) {
            $total += $item->kategori->point;
        }
        return $total;
    }
}
