<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Category;

class TransactionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_belongs_to_user_and_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $t = Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);

        $this->assertInstanceOf(User::class, $t->user);
        $this->assertInstanceOf(Category::class, $t->category);
        $this->assertEquals($user->id, $t->user->id);
        $this->assertEquals($category->id, $t->category->id);
    }

    public function test_amount_cast_and_fillable()
    {
        $t = Transaction::factory()->make(['amount' => 123.456]);
        $this->assertEquals('123.46', (string) $t->amount);
    }
}
