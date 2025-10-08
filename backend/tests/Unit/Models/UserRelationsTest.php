<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;

class UserRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_transactions_and_categories()
    {
        $user = User::factory()->create();
        Category::factory()->count(2)->create(['user_id' => $user->id]);
        Transaction::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->categories);
        $this->assertCount(3, $user->transactions);
    }
}
