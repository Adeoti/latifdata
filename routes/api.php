<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateWithApiToken;
use App\Http\Controllers\Api\V1\UserBalanceController;
use App\Http\Controllers\Api\V1\DecoderVerificationController;
use App\Http\Controllers\Api\V1\MeterVerificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('v1')->group(function () {

    Route::get('balance', [UserBalanceController::class, 'getBalance'])->middleware(AuthenticateWithApiToken::class);
    Route::post('verify-decoder', [DecoderVerificationController::class, 'verifyDecoder'])->middleware(AuthenticateWithApiToken::class);
    Route::post('verify-meter', [MeterVerificationController::class, 'verifyMeter'])->middleware(AuthenticateWithApiToken::class);

});
