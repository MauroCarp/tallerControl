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

// Página de diagnóstico Firefox para push notifications
Route::get('/firefox-debug', function () {
    return view('firefox-debug');
});

// Gestión de suscripciones push (requiere autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/push-manager', [\App\Http\Controllers\PushSubscriptionManagerController::class, 'index']);
    Route::get('/push-manager/user-subscriptions', [\App\Http\Controllers\PushSubscriptionManagerController::class, 'getUserSubscriptions']);
    Route::post('/push-manager/delete-user-subscriptions', [\App\Http\Controllers\PushSubscriptionManagerController::class, 'deleteUserSubscriptions']);
    Route::post('/push-manager/delete-subscription', [\App\Http\Controllers\PushSubscriptionManagerController::class, 'deleteSubscription']);
    Route::get('/push-manager/stats', [\App\Http\Controllers\PushSubscriptionManagerController::class, 'getStats']);
    Route::post('/push-manager/clean-all', [\App\Http\Controllers\PushSubscriptionManagerController::class, 'cleanAllSubscriptions']);
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

// Test de Mantenimiento General - Push Notifications
Route::get('/test-mantenimiento', [\App\Http\Controllers\TestMantenimientoController::class, 'index']);
Route::post('/test-mantenimiento/crear', [\App\Http\Controllers\TestMantenimientoController::class, 'crearMantenimientoPrueba']);
Route::post('/test-mantenimiento/crear-usuario-6', [\App\Http\Controllers\TestMantenimientoController::class, 'crearUsuario6']);
Route::post('/test-mantenimiento/crear-suscripcion', [\App\Http\Controllers\TestMantenimientoController::class, 'crearSuscripcionPrueba']);
Route::get('/test-mantenimiento/estado', [\App\Http\Controllers\TestMantenimientoController::class, 'obtenerEstado']);
Route::post('/test-mantenimiento/limpiar', [\App\Http\Controllers\TestMantenimientoController::class, 'limpiarDatosPrueba']);

// Push Notifications Routes
Route::get('/push/vapid-public-key', [PushNotificationController::class, 'getVapidPublicKey']);
Route::post('/push/verify-subscription', [PushNotificationController::class, 'verifySubscription']);

// Allow both authenticated and test subscriptions
Route::post('/push/subscribe', [PushNotificationController::class, 'subscribe']);
Route::post('/push-subscriptions', [PushNotificationController::class, 'subscribe']); // Ruta alternativa para el toast

// Test routes - these should be public for testing, but secure in production
Route::post('/push/send-test', [PushNotificationController::class, 'sendTest']);
Route::post('/push/test', [PushNotificationController::class, 'sendTest']); // Ruta alternativa para el toast

// Protected routes that require authentication (for production features)
Route::middleware('auth')->group(function () {
    // Add any production-specific push notification routes here
    // Route::post('/push/subscribe-authenticated', [PushNotificationController::class, 'subscribeAuthenticated']);
});
