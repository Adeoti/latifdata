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

Route::get('app/share-wallet', function () {
    return;
});
Route::post('/monnify/webhook/adeotisweetbill/ibadan/startmilkbread/eran-ileya', [WebhookMonnifyController::class, 'handleWebhook']);

// Route::get('/login', function () {
//     return redirect(route('filament.admin.auth.login'));
// })->name('login');
