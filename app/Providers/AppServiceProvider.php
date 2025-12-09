<?php

namespace App\Providers;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Policies\LabelPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TaskStatusPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\URL;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        TaskStatus::class => TaskStatusPolicy::class,
        Task::class => TaskPolicy::class,
        Label::class => LabelPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        app()->setLocale('ru');

        if (config('app.env') != 'local') {
            URL::forceScheme('https');
        }
    }
}
