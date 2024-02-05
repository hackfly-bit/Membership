<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray($request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,            
            'email_verified_at' => dateTimeFormat($this->email_verified_at),
            'role' => $this->role,
            'token_wa' => $this->token_wa,
            'created_at' => dateTimeFormat($this->created_at),
            'updated_at' => dateTimeFormat($this->updated_at),
        ];
    }
}
