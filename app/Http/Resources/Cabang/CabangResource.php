<?php

namespace App\Http\Resources\Cabang;

use Illuminate\Http\Resources\Json\JsonResource;

class CabangResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nama_cabang' => $this->nama_cabang,
			'code_cabang' => $this->code_cabang,
			'alamat' => $this->alamat,
			'nohp' => $this->nohp,
            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
        ];
    }
}
