<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Laravel\Passport\Passport;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_categories_grouped_by_type()
    {
        $user = User::factory()->create();
        Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);
        Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    Passport::actingAs($user, ['*']);
    $this->actingAs($user, 'api');

        $response = $this->getJson('/api/categories');
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('income', $json);
        $this->assertArrayHasKey('expense', $json);
    }

    public function test_store_creates_category()
    {
        $user = User::factory()->create();
    Passport::actingAs($user, ['*']);
    $this->actingAs($user, 'api');
        $data = [
            'name' => 'Food',
            'type' => 'expense',
        ];
        $response = $this->postJson('/api/categories', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['name' => 'Food', 'user_id' => $user->id]);
    }

    public function test_store_validation_error()
    {
        $user = User::factory()->create();
    Passport::actingAs($user, ['*']);
    $this->actingAs($user, 'api');
        $response = $this->postJson('/api/categories', ['name' => '']);
        $response->assertStatus(422);
    }

    public function test_update_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
    Passport::actingAs($user, ['*']);
    $this->actingAs($user, 'api');
        $response = $this->putJson('/api/categories/' . $category->id, ['name' => 'Updated', 'type' => $category->type]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Updated']);
    }

    public function test_destroy_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
    Passport::actingAs($user, ['*']);
    $this->actingAs($user, 'api');
        $response = $this->deleteJson('/api/categories/' . $category->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_filter_and_pagination()
    {
        $user = User::factory()->create();
        Category::factory()->count(15)->create(['user_id' => $user->id]);
        Passport::actingAs($user, ['*']);
        $response = $this->getJson('/api/categories?per_page=10');
        $response->assertStatus(200);
    }
}
