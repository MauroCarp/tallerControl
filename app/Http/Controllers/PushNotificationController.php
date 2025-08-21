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
            $subscription = $this->pushService->createSubscription(
                Auth::id() ?? 1, // Si no hay usuario autenticado, usar ID 1
                $request->all()
            );

            return response()->json([
                'message' => 'Suscripción creada exitosamente',
                'subscription_id' => $subscription->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la suscripción'], 500);
        }
    }

    /**
     * Enviar una notificación de prueba
     */
    public function sendTest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
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
                $successCount = $this->pushService->sendToUser($request->user_id, $payload);
                \Log::info("Enviando a usuario {$request->user_id}, éxito: {$successCount}");
            } else {
                // Enviar a todos los usuarios
                $successCount = $this->pushService->sendToAll($payload);
                \Log::info("Enviando a todos, éxito: {$successCount}");
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
