<?php

use App\Http\Controllers\Api\MermaHumedadController;
use App\Http\Controllers\PushNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin');
});

// Página de prueba para push notifications
Route::get('/push-test', function () {
    return view('push-test');
});

// Página de prueba para API
Route::get('/test-api', function () {
    return view('test-api');
});

Route::post('/merma-humedad', [MermaHumedadController::class, 'getMermaHumedad']);

// Push Notifications Routes
Route::get('/push/vapid-public-key', [PushNotificationController::class, 'getVapidPublicKey']);
Route::post('/push/subscribe', [PushNotificationController::class, 'subscribe']);
Route::post('/push/send-test', [PushNotificationController::class, 'sendTest']);
