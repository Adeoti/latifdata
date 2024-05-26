<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserBalanceController;
use App\Http\Middleware\AuthenticateWithApiToken;

//Route::prefix('v1')->group(function () {
    Route::get('/api/v1/balance', [UserBalanceController::class, 'getBalance'])->middleware(AuthenticateWithApiToken::class);
//});


Route::get('/hiw', function(){
    return "Test here...";
});