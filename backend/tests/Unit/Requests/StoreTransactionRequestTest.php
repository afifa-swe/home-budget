<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Category;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class StoreTransactionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_transaction_request_accepts_valid_data()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $req = new StoreTransactionRequest();
        $req->setUserResolver(function () use ($user) { return $user; });

        $data = [
            'occurred_at' => now()->toDateString(),
            'type' => 'income',
            'category_id' => $category->id,
            'amount' => 10.5,
        ];

        $rules = $req->rules();

        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        $this->assertFalse($validator->fails());
    }

    public function test_store_transaction_request_rejects_invalid_category()
    {
        $user = User::factory()->create();
        $req = new StoreTransactionRequest();
        $req->setUserResolver(function () use ($user) { return $user; });

        $data = [
            'occurred_at' => now()->toDateString(),
            'type' => 'income',
            'category_id' => 99999,
            'amount' => 10.5,
        ];

        $this->expectException(ValidationException::class);
        \Illuminate\Support\Facades\Validator::validate($data, $req->rules());
    }
}
