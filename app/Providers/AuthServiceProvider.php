<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [

        'App\Project' => 'App\Policies\ProjectPolicy',
        'App\Task' => 'App\Policies\TaskPolicy',

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // registers policies
        $this->registerPolicies();

        // run the logic in the gate before all other auth checks
        Gate::before(function ($user, $ability) {
            return $user->isAdmin();
        });
    }
}
