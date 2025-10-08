<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Category;
use App\Models\User;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_belongs_to_user()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $category->user);
        $this->assertEquals($user->id, $category->user->id);
    }

    public function test_fillable_and_create()
    {
        $user = User::factory()->create();
        $data = ['name' => 'TestCat', 'type' => 'income', 'user_id' => $user->id];
        $c = Category::create($data);

        $this->assertDatabaseHas('categories', ['name' => 'TestCat', 'user_id' => $user->id]);
        $this->assertEquals('TestCat', $c->name);
    }
}
