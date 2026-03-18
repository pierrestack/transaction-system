<?php

use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('transactions')->group(function () {

    Route::controller(TransactionController::class)->group(function () {

        Route::post('/deposit', 'deposit');
        
    });
});