<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Catalog\Services\CatalogService;
use Modules\Membership\Services\MemberService;
use Modules\Shared\Contracts\BookServiceInterface;
use Modules\Shared\Contracts\MemberServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registrasi contract binding antar bounded context.
     */
    public function register(): void
    {
        // Catalog Context menyediakan layanan buku untuk context lain
        $this->app->bind(BookServiceInterface::class, CatalogService::class);

        // Membership Context menyediakan layanan anggota untuk context lain
        $this->app->bind(MemberServiceInterface::class, MemberService::class);
    }

    public function boot(): void
    {
        //
    }
}
