<?php

use App\Http\Controllers\Api\AuthController as ApiAuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {

   Route::controller(ApiAuthController::class)->group(function () {

        Route::post('/login', 'login')->middleware('throttle:5,1');

        Route::post('/logout', 'logout')->middleware('auth:sanctum');
        
    }); 

});