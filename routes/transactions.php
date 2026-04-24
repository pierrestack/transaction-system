<?php

use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('transactions')->group(function () {

    Route::controller(TransactionController::class)->group(function () {

        Route::post('/init-deposit', 'initDeposit');

        Route::post('/execute-deposit', 'executeDeposit');

        Route::post('/init-withdrawal', 'initWithdrawal');

        Route::post('/execute-withdrawal', 'executeWithdrawal');

        Route::post('/init-transfer', 'initMonoTransfer');

        Route::post('/execute-transfer', 'executeMonoTransfer');

        Route::post('/init-multi-transfer', 'initMultiTransfer');

        Route::post('/execute-multi-transfer', 'executeMultiTransfer');
    });
});
