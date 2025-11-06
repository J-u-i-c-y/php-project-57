<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use App\Models\TaskStatus;
use App\Policies\TaskStatusPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
        if (config('app.env') != 'local') {
            URL::forceScheme('https');
        }
    }

    protected $policies = [
        TaskStatus::class => TaskStatusPolicy::class,
    ];
}
