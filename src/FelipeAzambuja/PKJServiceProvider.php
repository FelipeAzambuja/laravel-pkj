<?php

namespace FelipeAzambuja;

use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;

class PKJServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        include __DIR__ . '/database.php';
    }
}
