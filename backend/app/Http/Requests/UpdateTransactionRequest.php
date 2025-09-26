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
            'category_id' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if ($value !== null) {
                        $userId = $this->user()->id;
                        $exists = \App\Models\Category::where('id', $value)->where('user_id', $userId)->exists();
                        if (!$exists) {
                            $fail('Категория не найдена или не принадлежит пользователю.');
                        }
                    }
                }
            ],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
