<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'occurred_at' => ['sometimes', 'date'],
            'type' => ['sometimes', 'in:income,expense'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
