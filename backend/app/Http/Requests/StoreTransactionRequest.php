<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'occurred_at' => ['required','date'],
            'type' => ['required','in:income,expense'],
            'category' => ['required','string'],
            'amount' => ['required','numeric','min:0.01'],
            'comment' => ['nullable','string'],
        ];
    }
}
