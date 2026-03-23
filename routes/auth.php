<?php

use App\Http\Controllers\Api\AuthController as ApiAuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {

   Route::controller(ApiAuthController::class)->group(function () {

        Route::middleware('throttle:5,1')->post('/login', 'login');

        Route::middleware('auth:sanctum')->post('/logout', 'logout');
        
    }); 

});