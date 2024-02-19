<?php

namespace App\Providers;

// use App\Models\Center;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\EloquentUserProvider;
use App\Models\Center;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('eloquent', function ($app, $config) {
            return new EloquentUserProvider($app['hash'], Center::class);
        });
    }
}
