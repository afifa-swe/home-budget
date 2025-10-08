<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use Illuminate\Http\Request;

class AuthControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user_and_issues_token_when_client_missing_returns_500()
    {
        config(['services.passport.password_client_id' => null, 'services.passport.password_client_secret' => null]);

        $controller = new AuthController();

        $req = Request::create('/register', 'POST', [
            'name' => 'Unit User',
            'email' => 'unit@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $resp = $controller->register($req);

        $this->assertDatabaseHas('users', ['email' => 'unit@example.com']);
        $this->assertEquals(500, $resp->getStatusCode());
    }

    public function test_issue_token_returns_response_object()
    {
        $controller = new AuthController();
        config(['services.passport.password_client_id' => '1', 'services.passport.password_client_secret' => 'secret']);

        $req = Request::create('/login', 'POST', ['username' => 'x', 'password' => 'y']);

    $resp = $this->invokeIssueToken($controller, $req);
    $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $resp);
    }

    protected function invokeIssueToken($controller, $req)
    {
        $ref = new \ReflectionClass($controller);
        $method = $ref->getMethod('issueToken');
        $method->setAccessible(true);
        return $method->invoke($controller, $req);
    }
}
