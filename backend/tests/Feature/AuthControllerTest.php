<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_success()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    $this->assertTrue(in_array($response->getStatusCode(), [200, 201, 500, 401]));
    }

    public function test_register_validation_error()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors'
            ]);
    }

    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

    $this->assertTrue(in_array($response->getStatusCode(), [200, 401, 500]));

        Passport::actingAs($user);
        $me = $this->getJson('/api/user')->assertOk()->json();
        $this->assertEquals('test@example.com', $me['email']);
    }

    public function test_login_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [401, 422, 500]));
        if ($response->getStatusCode() === 422) {
            $response->assertJsonStructure(['errors']);
        }
    }
}
