<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use Laravel\Passport\Passport;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_transactions()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        Transaction::factory()->count(5)->create(['user_id' => $user->id, 'category_id' => $category->id]);
    Passport::actingAs($user, ['*']);
    $this->actingAs($user, 'api');
        $response = $this->getJson('/api/transactions');
        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }

    public function test_store_creates_transaction()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
    Passport::actingAs($user, ['*']);
    $this->actingAs($user, 'api');
        $data = [
            'amount' => 100.50,
            'type' => 'income',
            'category_id' => $category->id,
            'occurred_at' => now()->toDateString(),
        ];
        $response = $this->postJson('/api/transactions', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('transactions', ['user_id' => $user->id, 'amount' => 100.50]);
    }

    public function test_store_validation_error()
    {
        $user = User::factory()->create();
    Passport::actingAs($user, ['*']);
    $this->actingAs($user, 'api');
        $response = $this->postJson('/api/transactions', [
            'amount' => '',
            'type' => '',
            'category_id' => null,
            'occurred_at' => '',
        ]);
        $response->assertStatus(422);
    }

    public function test_update_transaction()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $transaction = Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);
    Passport::actingAs($user, ['*']);
    $this->actingAs($user, 'api');
        $response = $this->putJson('/api/transactions/' . $transaction->id, [
            'amount' => 200.00,
            'type' => $transaction->type,
            'category_id' => $category->id,
            'occurred_at' => now()->toDateString(),
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'amount' => 200.00]);
    }

    public function test_destroy_transaction()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $transaction = Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);
    Passport::actingAs($user, ['*']);
    $this->actingAs($user, 'api');
        $response = $this->deleteJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    public function test_filter_and_pagination()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        Transaction::factory()->count(15)->create(['user_id' => $user->id, 'category_id' => $category->id]);
        Passport::actingAs($user, ['*']);
        $response = $this->getJson('/api/transactions?month=' . now()->format('Y-m'));
        $response->assertStatus(200);
    }
}
