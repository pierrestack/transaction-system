<?php

namespace App\Providers;

use App\Http\Controllers\Api\TransactionController;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Eloquent\CrossTransactionRepository;
use App\Repositories\Eloquent\SameTransactionRepository;
use App\Repositories\Eloquent\TransactionRepository;
use App\Resolvers\TransferProcessorResolver;
use App\Services\Contracts\ConvertionCurrencyInterface;
use App\Services\Contracts\FeeCalculatorInterface;
use App\Services\Currency\ConvertCurrencyService;
use App\Services\Fee\FeeCalculatorService;
use App\Services\OperationService;
use App\Services\TransactionService;
use App\Services\TransferProcessor\CrossCurrencyTransferProcessor;
use App\Services\TransferProcessor\SameCurrencyTransferProcessor;
use App\Services\TransferProcessor\TransactionProcessor;
use App\Services\Validator\TransferValidator;
use Illuminate\Support\ServiceProvider;

class TransactionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FeeCalculatorInterface::class, FeeCalculatorService::class);

        $this->app->bind(ConvertionCurrencyInterface::class, ConvertCurrencyService::class);

        $this->app->when(SameCurrencyTransferProcessor::class)
            ->needs(TransactionRepository::class)
            ->give(SameTransactionRepository::class);

        $this->app->when(CrossCurrencyTransferProcessor::class)
            ->needs(TransactionRepository::class)
            ->give(CrossTransactionRepository::class);

        $this->app->bind(TransferProcessorResolver::class, function ($app) {
            return new TransferProcessorResolver([
                $app->make(SameCurrencyTransferProcessor::class),
                $app->make(CrossCurrencyTransferProcessor::class),
            ]);
        });

        $this->app->bind(TransactionRepositoryInterface::class, function ($app) {
            return new SameTransactionRepository(
                $app->make(FeeCalculatorInterface::class),
                $app->make(OperationService::class),
                $app->make(TransferValidator::class),
            );
        });

        $this->app->when(TransactionController::class)
            ->needs(TransactionService::class)
            ->give(function ($app) {
                return new TransactionService(
                    new SameCurrencyTransferProcessor($app->make(SameTransactionRepository::class)),
                    $app->make(TransferProcessorResolver::class)
                );
            });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
