<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Models\Project;

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
        // Configure route model binding for Project to use project_id instead of id
        Route::bind('project', function ($value) {
            $project = Project::where('project_id', $value)->first();
            if (!$project) {
                abort(404, 'Project not found');
            }
            return $project;
        });
    }
}
