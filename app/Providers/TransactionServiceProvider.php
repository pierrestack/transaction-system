<?php

namespace App\Providers;

use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Eloquent\SameTransactionRepository;
use App\Services\Contracts\FeeCalculatorInterface;
use App\Services\Fee\FeeCalculatorService;
use Illuminate\Support\ServiceProvider;

class TransactionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(TransactionRepositoryInterface::class, SameTransactionRepository::class);
        $this->app->bind(FeeCalculatorInterface::class, FeeCalculatorService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
