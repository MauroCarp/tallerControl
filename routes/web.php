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

// Página de diagnóstico Chrome para push notifications
Route::get('/chrome-debug', function () {
    return view('chrome-debug');
});

// Página de prueba para toast de notificaciones
Route::get('/toast-test', function () {
    return view('toast-test');
});

// Página de prueba para API
Route::get('/test-api', function () {
    return view('test-api');
});

Route::post('/merma-humedad', [MermaHumedadController::class, 'getMermaHumedad']);

// Push Notifications Routes
Route::get('/push/vapid-public-key', [PushNotificationController::class, 'getVapidPublicKey']);

// Rutas protegidas que requieren autenticación
Route::middleware('auth')->group(function () {
    Route::post('/push/subscribe', [PushNotificationController::class, 'subscribe']);
    Route::post('/push-subscriptions', [PushNotificationController::class, 'subscribe']); // Ruta alternativa para el toast
});

// Rutas de testing (pueden ser públicas para pruebas, pero agregar auth en producción)
Route::post('/push/send-test', [PushNotificationController::class, 'sendTest']);
Route::post('/push/test', [PushNotificationController::class, 'sendTest']); // Ruta alternativa para el toast
