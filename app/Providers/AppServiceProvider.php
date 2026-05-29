<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manager-only', fn(User $user) => $user->role === 'manager');
        Gate::define('admin-only', fn(User $user) => $user->role === 'admin');
        Gate::define('staff-access', fn(User $user) => $user->role === 'user' || $user->role === 'admin');
    }
}
