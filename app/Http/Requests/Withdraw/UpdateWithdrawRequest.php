<?php

namespace App\Http\Requests\Withdraw;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWithdrawRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes'],
			'point' => ['sometimes'],
			'wd_reason' => ['sometimes', 'string'],
        ];
    }
}
