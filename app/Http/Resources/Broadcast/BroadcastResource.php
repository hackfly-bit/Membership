<?php

namespace App\Http\Resources\Broadcast;

use Illuminate\Http\Resources\Json\JsonResource;

class BroadcastResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'api_status' => $this->api_status,
			'detail' => $this->detail,
			'process' => $this->process,
			'status' => $this->status,
			'target' => $this->target,
			'reason' => $this->reason,
            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
        ];
    }
}
