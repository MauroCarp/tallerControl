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
        
        // Remove middleware requirement for subscribe to allow testing
        // Authentication will be checked within the method
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
            // Get user ID if authenticated, otherwise use null for anonymous subscriptions
            $userId = Auth::check() ? Auth::id() : null;
            
            // For production, you might want to require authentication
            // For testing purposes, allow anonymous subscriptions with null user_id
            if (!$userId) {
                // Create anonymous subscription - in production you might want to restrict this
                \Log::info('Creating anonymous subscription for testing');
            }
            
            $subscription = $this->pushService->createSubscription(
                $userId,
                $request->all()
            );

            return response()->json([
                'message' => 'Suscripción creada exitosamente',
                'subscription_id' => $subscription->id,
                'user_id' => $userId,
                'is_authenticated' => Auth::check(),
                'is_anonymous' => !Auth::check()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la suscripción: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Verificar si existe una suscripción
     */
    public function verifySubscription(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Endpoint requerido'], 400);
        }

        try {
            $endpoint = $request->endpoint;
            $endpointHash = hash('sha256', $endpoint);
            
            \Log::info('Verificando suscripción', [
                'endpoint_preview' => substr($endpoint, 0, 50) . '...',
                'endpoint_hash' => $endpointHash,
                'user_id' => Auth::id(),
                'is_authenticated' => Auth::check()
            ]);
            
            // Buscar suscripción por endpoint hash
            $subscription = \App\Models\PushSubscription::where('endpoint_hash', $endpointHash)->first();
            
            if ($subscription) {
                \Log::info('Suscripción encontrada', [
                    'subscription_id' => $subscription->id,
                    'subscription_user_id' => $subscription->user_id,
                    'current_user_id' => Auth::id()
                ]);
                
                // Si está autenticado, verificar que sea del usuario actual
                if (Auth::check() && $subscription->user_id && $subscription->user_id !== Auth::id()) {
                    \Log::warning('Suscripción pertenece a diferente usuario', [
                        'subscription_user' => $subscription->user_id,
                        'current_user' => Auth::id()
                    ]);
                    
                    return response()->json([
                        'exists' => false,
                        'reason' => 'subscription_belongs_to_different_user',
                        'debug' => [
                            'subscription_user_id' => $subscription->user_id,
                            'current_user_id' => Auth::id()
                        ]
                    ]);
                }
                
                return response()->json([
                    'exists' => true,
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'is_current_user' => Auth::check() && $subscription->user_id === Auth::id(),
                    'debug' => [
                        'endpoint_hash' => $endpointHash,
                        'subscription_user_id' => $subscription->user_id,
                        'current_user_id' => Auth::id()
                    ]
                ]);
            }
            
            \Log::info('Suscripción no encontrada', [
                'endpoint_hash' => $endpointHash,
                'total_subscriptions' => \App\Models\PushSubscription::count()
            ]);
            
            return response()->json([
                'exists' => false,
                'reason' => 'subscription_not_found',
                'debug' => [
                    'endpoint_hash' => $endpointHash,
                    'total_subscriptions_in_db' => \App\Models\PushSubscription::count(),
                    'user_id' => Auth::id()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error verificando suscripción', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error verificando suscripción: ' . $e->getMessage(),
                'debug' => [
                    'user_id' => Auth::id(),
                    'is_authenticated' => Auth::check()
                ]
            ], 500);
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
