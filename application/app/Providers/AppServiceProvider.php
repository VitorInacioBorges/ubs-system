<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Registra que as migrations dentro de ./migrations/* devem ser rodadas

        $mainPath = database_path('migrations');
        $directories = glob($mainPath . '/*', GLOB_ONLYDIR);

        $this->loadMigrationsFrom(array_merge([$mainPath], $directories));
    }
}
