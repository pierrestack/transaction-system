<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\TransactionServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    TransactionServiceProvider::class,
];
