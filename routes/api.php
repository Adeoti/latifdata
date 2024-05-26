<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateWithApiToken;
use App\Http\Controllers\Api\V1\UserBalanceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('v1')->group(function () {
    Route::get('balance', [UserBalanceController::class, 'getBalance'])->middleware(AuthenticateWithApiToken::class);
});
