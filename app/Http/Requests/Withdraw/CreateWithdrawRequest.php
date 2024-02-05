<?php

namespace App\Http\Requests\Withdraw;

use Illuminate\Foundation\Http\FormRequest;

class CreateWithdrawRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => ['required'],
			'point' => ['required'],
			'wd_reason' => ['nullable', 'string'],
        ];
    }
}
