<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\User;
use App\Models\Category;
use Illuminate\Validation\ValidationException;

class UpdateTransactionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_transaction_request_accepts_partial_data()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $req = new UpdateTransactionRequest();
        $req->setUserResolver(function () use ($user) { return $user; });

        $data = [
            'amount' => 20.00,
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($data, $req->rules());
        $this->assertFalse($validator->fails());
    }

    public function test_update_transaction_request_rejects_bad_category()
    {
        $user = User::factory()->create();
        $req = new UpdateTransactionRequest();
        $req->setUserResolver(function () use ($user) { return $user; });

        $data = ['category_id' => 99999];

        $this->expectException(ValidationException::class);
        \Illuminate\Support\Facades\Validator::validate($data, $req->rules());
    }
}
