<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    Modules\Auth\Providers\AuthServiceProvider::class,
    Modules\Catalog\Providers\CatalogServiceProvider::class,
    Modules\Membership\Providers\MembershipServiceProvider::class,
    Modules\Circulation\Providers\CirculationServiceProvider::class,
];
