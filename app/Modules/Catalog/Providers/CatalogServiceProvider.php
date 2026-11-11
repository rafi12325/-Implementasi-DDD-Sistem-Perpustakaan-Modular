<?php

namespace Modules\Catalog\Providers;

use Illuminate\Support\ServiceProvider;

class CatalogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Views', 'catalog');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
