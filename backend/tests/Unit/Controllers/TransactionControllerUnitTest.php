<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\TransactionController;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\Validation\ValidationException;

class TransactionControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_transactions_with_running_balance_and_category_name()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cat = Category::factory()->create(['user_id' => $user->id, 'name' => 'Cat1']);
        Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id, 'type' => 'income', 'amount' => 100, 'occurred_at' => now()->subDays(2)]);
        Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id, 'type' => 'expense', 'amount' => 40, 'occurred_at' => now()->subDays(1)]);

    $controller = new TransactionController();
    $req = Request::create('/fake', 'GET');
    $req->setUserResolver(function () use ($user) { return $user; });
    $resp = $controller->index($req);
        $this->assertEquals(200, $resp->getStatusCode());
        $rows = json_decode($resp->getContent(), true);
        $this->assertCount(2, $rows);
        $this->assertArrayHasKey('running_balance', $rows[0]);
        $this->assertArrayHasKey('category', $rows[0]);
    }

    public function test_store_creates_transaction_and_validates_category_exists_for_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $cat = Category::factory()->create(['user_id' => $user->id]);

        $controller = new TransactionController();
        $data = [
            'occurred_at' => now()->toDateString(),
            'type' => 'income',
            'category_id' => $cat->id,
            'amount' => 55.5,
        ];

        $req = new class($data) extends StoreTransactionRequest {
            protected $validatedData;
            public function __construct($data = []) { $this->validatedData = $data; }
            public function validated($key = null, $default = null): array { return $this->validatedData; }
            public function all($keys = null) { return $this->validatedData; }
        };
        $req->setUserResolver(function () use ($user) { return $user; });
        $resp = $controller->store($req);
        $this->assertEquals(201, $resp->getStatusCode());
        $this->assertDatabaseHas('transactions', ['user_id' => $user->id, 'amount' => 55.5]);

        $this->expectException(ValidationException::class);
        $form = new StoreTransactionRequest();
        $form->setUserResolver(function () use ($user) { return $user; });
        $bad = [
            'occurred_at' => now()->toDateString(),
            'type' => 'income',
            'category_id' => 99999,
            'amount' => 10,
        ];
        \Illuminate\Support\Facades\Validator::validate($bad, $form->rules());
    }

    public function test_update_updates_transaction_and_checks_ownership()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $cat = Category::factory()->create(['user_id' => $user->id]);
        $t = Transaction::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id]);

    $controller = new TransactionController();
        $updData = ['amount' => 200.00];
        $upreq = new class($updData) extends UpdateTransactionRequest {
            protected $validatedData;
            public function __construct($data = []) { $this->validatedData = $data; }
            public function validated($key = null, $default = null): array { return $this->validatedData; }
            public function all($keys = null) { return $this->validatedData; }
        };
        $upreq->setUserResolver(function () use ($user) { return $user; });
        $resp = $controller->update($upreq, $t->id);
        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertDatabaseHas('transactions', ['id' => $t->id, 'amount' => 200.00]);
    }

    public function test_destroy_deletes_transaction_for_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $t = Transaction::factory()->create(['user_id' => $user->id]);

        $controller = new TransactionController();
        $req = Request::create('/fake', 'DELETE');
        $req->setUserResolver(function () use ($user) { return $user; });

        $resp = $controller->destroy($req, $t->id);
        $this->assertEquals(204, $resp->getStatusCode());
        $this->assertDatabaseMissing('transactions', ['id' => $t->id]);
    }
}
