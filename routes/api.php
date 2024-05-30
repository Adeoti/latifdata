<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateWithApiToken;
use App\Http\Controllers\Api\V1\AirtimeController;
use App\Http\Controllers\Api\V1\CableController;
use App\Http\Controllers\Api\V1\DataController;
use App\Http\Controllers\Api\V1\UserBalanceController;
use App\Http\Controllers\Api\V1\MeterVerificationController;
use App\Http\Controllers\Api\V1\DecoderVerificationController;
use App\Http\Controllers\Api\V1\ElectricityController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('v1')->group(function () {

    Route::get('balance', [UserBalanceController::class, 'getBalance'])->middleware(AuthenticateWithApiToken::class);
    Route::post('verify-decoder', [DecoderVerificationController::class, 'verifyDecoder'])->middleware(AuthenticateWithApiToken::class);
    Route::post('verify-meter', [MeterVerificationController::class, 'verifyMeter'])->middleware(AuthenticateWithApiToken::class);
    Route::post('buy-airtime', [AirtimeController::class, 'buyAirtime'])->middleware(AuthenticateWithApiToken::class);
    Route::post('buy-data', [DataController::class, 'buyData'])->middleware(AuthenticateWithApiToken::class);
    Route::post('buy-electricity', [ElectricityController::class, 'buyElectricity'])->middleware(AuthenticateWithApiToken::class);
    Route::post('buy-cable', [CableController::class, 'buyCable'])->middleware(AuthenticateWithApiToken::class);

});
