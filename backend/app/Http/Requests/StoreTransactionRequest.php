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
            'category_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $userId = $this->user()->id;
                    $exists = \App\Models\Category::where('id', $value)->where('user_id', $userId)->exists();
                    if (!$exists) {
                        $fail('Категория не найдена или не принадлежит пользователю.');
                    }
                }
            ],
            'amount' => ['required','numeric','min:0.01'],
            'comment' => ['nullable','string'],
        ];
    }
}
