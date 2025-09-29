<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // enable the password grant so we can issue access + refresh tokens
        // Register passport routes and enable password grant
        // Some installations of Passport don't expose the routes() method in the
        // used class; guard the call to avoid a fatal error during app boot.
        if (method_exists(Passport::class, 'routes')) {
            Passport::routes();
        }
        // enable password grant support if available
        if (method_exists(Passport::class, 'enablePasswordGrant')) {
            Passport::enablePasswordGrant();
        }
    }
}
