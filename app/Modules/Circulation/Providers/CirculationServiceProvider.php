<?php

namespace Modules\Circulation\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Modules\Circulation\Events\BookBorrowed;
use Modules\Circulation\Listeners\LogBookBorrowed;

class CirculationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Views', 'circulation');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Registrasi Domain Event -> Listener
        Event::listen(BookBorrowed::class, LogBookBorrowed::class);
    }
}
