<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\StatsController;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;

class StatsControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_aggregates_income_and_expense_for_user()
    {
        $user = User::factory()->create();
        $cat = Category::factory()->create(['user_id' => $user->id]);
        Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id, 'type' => 'income', 'amount' => 100, 'occurred_at' => now()]);
        Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id, 'type' => 'expense', 'amount' => 40, 'occurred_at' => now()]);

        $this->actingAs($user);

        $controller = new StatsController();
        $req = Request::create('/fake', 'GET', ['year' => now()->year]);
        $resp = $controller->monthly($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $rows = json_decode($resp->getContent(), true);
        $this->assertNotEmpty($rows);
        $first = $rows[0];
        $this->assertArrayHasKey('total_income', $first);
        $this->assertArrayHasKey('total_expense', $first);
        $this->assertEquals('60.00', $first['balance']);
    }
}
