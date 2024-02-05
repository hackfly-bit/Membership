<?php

namespace App\Http\Requests\Broadcast;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBroadcastRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'api_status' => ['sometimes', 'string'],
			'detail' => ['sometimes', 'string'],
			'process' => ['sometimes', 'string'],
			'status' => ['sometimes', 'string'],
			'target' => ['sometimes', 'string'],
			'reason' => ['sometimes', 'string'],
        ];
    }
}
