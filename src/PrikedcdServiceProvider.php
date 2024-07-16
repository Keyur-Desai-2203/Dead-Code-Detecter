<?php

namespace Keyur\Prikedcd;

use Illuminate\Support\ServiceProvider;

class PrikedcdServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'prikedcd');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/prikedcd'),
        ]);

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/keyur/prikedcd'),
        ], 'public');
    }

    public function register()
    {
        //
    }
}
