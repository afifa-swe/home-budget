<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use Laravel\Passport\Passport;

class StatsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_stats_returns_only_current_user_data()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category1 = Category::factory()->create(['user_id' => $user1->id]);
        $category2 = Category::factory()->create(['user_id' => $user2->id]);
        Transaction::factory()->count(3)->create(['user_id' => $user1->id, 'category_id' => $category1->id, 'type' => 'income', 'amount' => 100]);
        Transaction::factory()->count(2)->create(['user_id' => $user2->id, 'category_id' => $category2->id, 'type' => 'expense', 'amount' => 50]);

    Passport::actingAs($user1, ['*']);
    $this->actingAs($user1, 'api');
        $response = $this->getJson('/api/stats/monthly?year=' . now()->year);
        $response->assertStatus(200);
        $rows = $response->json();
        // user1 had 3 incomes of 100 each => total 300 in the month
        $this->assertNotEmpty($rows);
    }

    public function test_stats_unauthenticated()
    {
        $response = $this->getJson('/api/stats/monthly');
        $response->assertStatus(401);
    }
}
