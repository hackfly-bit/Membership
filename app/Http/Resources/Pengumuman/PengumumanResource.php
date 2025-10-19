<?php

namespace App\Http\Resources\Pengumuman;

use Illuminate\Http\Resources\Json\JsonResource;

class PengumumanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
			'isi' => $this->isi,
            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
        ];
    }
}
