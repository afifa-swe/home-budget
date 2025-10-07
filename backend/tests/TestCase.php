<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        // Ensure an application key exists in the test environment. Some test
        // runners (CI or local) may not load a .env, which causes "No
        // application encryption key has been specified." errors. Generate a
        // temporary base64 key for the lifetime of the test run.
        if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'] ?? null) && empty($_SERVER['APP_KEY'] ?? null)) {
            // Generate a 32-byte random key and set it as base64 to match Laravel's format
            $key = base64_encode(random_bytes(32));
            putenv("APP_KEY=base64:{$key}");
            $_ENV['APP_KEY'] = "base64:{$key}";
            $_SERVER['APP_KEY'] = "base64:{$key}";
        }

        parent::setUp();
        // Keep middleware enabled so session and CSRF are available for Fortify web auth tests.
    }
}
