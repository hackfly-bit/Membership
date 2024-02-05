<?php

namespace App\Http\Requests\Broadcast;

use Illuminate\Foundation\Http\FormRequest;

class CreateBroadcastRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'api_status' => ['nullable', 'string'],
			'detail' => ['nullable', 'string'],
			'process' => ['nullable', 'string'],
			'status' => ['nullable', 'string'],
			'target' => ['nullable', 'string'],
			'reason' => ['nullable', 'string'],
        ];
    }
}
