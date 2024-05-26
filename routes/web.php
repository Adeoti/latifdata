<?php

use App\Models\PaymentIntegration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WebhookMonnifyController;


//Route::get('/', [HomeController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});


Route::post('/monnify/webhook/adeotisweetbill/ibadan/startmilkbread/eran-ileya', [WebhookMonnifyController::class, 'handleWebhook']);






