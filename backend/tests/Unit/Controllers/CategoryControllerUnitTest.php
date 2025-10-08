<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\CategoryController;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_groups_categories_by_type_for_user()
    {
        $user = User::factory()->create();
        Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);
        Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
        Category::factory()->create();

        $this->actingAs($user);

    $controller = new CategoryController();
    $req = Request::create('/fake', 'GET');
    $req->setUserResolver(function () use ($user) { return $user; });
    $resp = $controller->index($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $data = json_decode($resp->getContent(), true);
        $this->assertArrayHasKey('income', $data);
        $this->assertArrayHasKey('expense', $data);
        $this->assertCount(1, $data['income']);
        $this->assertCount(1, $data['expense']);
    }

    public function test_store_creates_category_and_validates_unique_per_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

    $controller = new CategoryController();
    $req = Request::create('/fake', 'POST', ['name' => 'Food', 'type' => 'expense']);
    $req->setUserResolver(function () use ($user) { return $user; });

    $resp = $controller->store($req);
        $this->assertEquals(201, $resp->getStatusCode());
        $this->assertDatabaseHas('categories', ['name' => 'Food', 'user_id' => $user->id]);

    $this->expectException(ValidationException::class);
    $req2 = Request::create('/fake', 'POST', ['name' => 'Food', 'type' => 'expense']);
    $req2->setUserResolver(function () use ($user) { return $user; });
    $controller->store($req2);
    }

    public function test_update_updates_and_enforces_unique_ignore_self()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $cat1 = Category::factory()->create(['user_id' => $user->id, 'name' => 'A']);
        $cat2 = Category::factory()->create(['user_id' => $user->id, 'name' => 'B']);

        $controller = new CategoryController();

        $this->expectException(ValidationException::class);
    $reqFail = Request::create('/fake', 'PUT', ['name' => 'A', 'type' => $cat2->type]);
    $reqFail->setUserResolver(function () use ($user) { return $user; });
    $controller->update($reqFail, $cat2->id);

    $reqOk = Request::create('/fake', 'PUT', ['name' => 'C', 'type' => $cat2->type]);
    $reqOk->setUserResolver(function () use ($user) { return $user; });
    $resp = $controller->update($reqOk, $cat2->id);
        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertDatabaseHas('categories', ['id' => $cat2->id, 'name' => 'C']);
    }

    public function test_destroy_deletes_category_for_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $cat = Category::factory()->create(['user_id' => $user->id]);

        $controller = new CategoryController();
        $req = Request::create('/fake', 'DELETE');
        $req->setUserResolver(function () use ($user) { return $user; });

        $resp = $controller->destroy($req, $cat->id);
        $this->assertEquals(204, $resp->getStatusCode());
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }
}
