<?php

namespace App\Http\Controllers;

use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PushNotificationController extends Controller
{
    private PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
        
        // Aplicar middleware de autenticación para las rutas de suscripción
        $this->middleware('auth')->only(['subscribe']);
    }

    /**
     * Obtener la clave pública VAPID
     */
    public function getVapidPublicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('app.vapid_public_key')
        ]);
    }

    /**
     * Subscribir un usuario a las push notifications
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Datos de suscripción inválidos'], 400);
        }

        try {
            // Asegurar que tenemos un usuario autenticado
            if (!Auth::check()) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }
            
            $subscription = $this->pushService->createSubscription(
                Auth::id(),
                $request->all()
            );

            return response()->json([
                'message' => 'Suscripción creada exitosamente',
                'subscription_id' => $subscription->id,
                'user_id' => Auth::id()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la suscripción: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Enviar una notificación de prueba
     */
    public function sendTest(Request $request): JsonResponse
    {
        // Log de entrada para debugging
        \Log::info("sendTest: Datos recibidos", [
            'title' => $request->title,
            'message' => $request->message,
            'user_id' => $request->user_id,
            'user_id_type' => gettype($request->user_id),
            'all_request_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            \Log::warning("sendTest: Validación fallida", ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $payload = [
            'title' => $request->title,
            'body' => $request->message,
            'icon' => '/images/icons/icon-192x192.png',
            'badge' => '/images/icons/icon-72x72.png',
            'tag' => 'test-notification',
            'data' => [
                'url' => url('/'),
                'timestamp' => now()->toISOString(),
            ]
        ];

        try {
            // Debug: verificar cuántas suscripciones hay
            $totalSubscriptions = \App\Models\PushSubscription::count();
            \Log::info("Total suscripciones en BD: " . $totalSubscriptions);
            
            if ($request->user_id) {
                // Enviar a un usuario específico
                \Log::info("Enviando notificación a usuario específico", [
                    'user_id' => $request->user_id,
                    'title' => $request->title,
                    'suscripciones_usuario' => \App\Models\PushSubscription::where('user_id', $request->user_id)->count()
                ]);
                
                $successCount = $this->pushService->sendToUser($request->user_id, $payload);
                \Log::info("Resultado envío a usuario específico", [
                    'user_id' => $request->user_id,
                    'success_count' => $successCount
                ]);
            } else {
                // Enviar a todos los usuarios
                \Log::info("Enviando notificación a todos los usuarios");
                $successCount = $this->pushService->sendToAll($payload);
                \Log::info("Resultado envío a todos", [
                    'success_count' => $successCount
                ]);
            }

            return response()->json([
                'message' => 'Notificación enviada',
                'notifications_sent' => $successCount,
                'total_subscriptions' => $totalSubscriptions
            ]);
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación: ' . $e->getMessage());
            return response()->json(['error' => 'Error al enviar la notificación: ' . $e->getMessage()], 500);
        }
    }
}
