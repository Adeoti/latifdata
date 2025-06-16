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
    abort(404);
    return;
});
Route::post('/wbhkmisttreyrfattnignccdsasdffghvnxxxxxcuit', [WebhookMonnifyController::class, 'handleWebhook']);

// Route::get('/login', function () {
//     return redirect(route('filament.admin.auth.login'));
// })->name('login');
