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
        include __DIR__ . '/database.php';
        include __DIR__ . '/Bind.php';
        $this->loadRoutesFrom(__DIR__.'/routes.php');

    }
}
