<?php

use App\Http\Controllers\PushNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Push Notifications API Routes
Route::prefix('push-notifications')->group(function () {
    // Ruta pública para obtener la clave VAPID (no requiere autenticación)
    Route::get('/vapid-public-key', [PushNotificationController::class, 'getVapidPublicKey'])->name('api.push.vapid');
    
    // Rutas que requieren autenticación
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/subscribe', [PushNotificationController::class, 'subscribe'])->name('api.push.subscribe');
        Route::post('/unsubscribe', [PushNotificationController::class, 'unsubscribe'])->name('api.push.unsubscribe');
        Route::post('/send-test', [PushNotificationController::class, 'sendTest'])->name('api.push.test');
    });
});
