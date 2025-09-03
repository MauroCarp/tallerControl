<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private WebPush $webPush;

    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject' => config('app.vapid_subject'),
                'publicKey' => config('app.vapid_public_key'),
                'privateKey' => config('app.vapid_private_key'),
            ],
        ];

        $defaultOptions = [];
        $timeout = 30;
        
        // Configuración SSL para cURL - se pasa como clientOptions al constructor
        $clientOptions = [];
        $verifySSL = env('PUSH_VERIFY_SSL', true);
        Log::info('SSL verification setting', ['verify_ssl' => $verifySSL]);
        
        if (!$verifySSL) {
            $clientOptions = [
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 10,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]
            ];
            Log::info('SSL verification disabled for push notifications with CURL options');
        }

        $this->webPush = new WebPush($auth, $defaultOptions, $timeout, $clientOptions);
    }

    /**
     * Enviar notificación a una suscripción específica
     */
    public function sendToSubscription(PushSubscription $subscription, array $payload): bool
    {
        try {
            // Verificar que tenemos las claves VAPID correctas
            $publicKey = config('app.vapid_public_key');
            $privateKey = config('app.vapid_private_key');
            
            if (empty($publicKey) || empty($privateKey)) {
                Log::error('VAPID keys not configured');
                return false;
            }
            
            Log::info('Sending push notification', [
                'endpoint' => $subscription->endpoint,
                'public_key_length' => strlen($publicKey),
                'private_key_length' => strlen($privateKey)
            ]);
            
            $subscription_data = Subscription::create($subscription->toWebPushFormat());
            
            $result = $this->webPush->sendOneNotification(
                $subscription_data,
                json_encode($payload)
            );

            if (!$result->isSuccess()) {
                Log::error('Push notification failed', [
                    'endpoint' => $subscription->endpoint,
                    'reason' => $result->getReason(),
                    'status_code' => $result->getResponse() ? $result->getResponse()->getStatusCode() : 'no response',
                ]);
                
                // Si la suscripción es inválida, eliminarla
                if ($result->isSubscriptionExpired()) {
                    $subscription->delete();
                }
                
                return false;
            }

            Log::info('Push notification sent successfully', [
                'subscription_id' => $subscription->id
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Push notification exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'subscription' => $subscription->id,
            ]);
            return false;
        }
    }

    /**
     * Enviar notificación a un usuario específico
     */
    public function sendToUser(int $userId, array $payload): int
    {
        $subscriptions = PushSubscription::where('user_id', $userId)->get();
        Log::info("sendToUser: Buscando suscripciones para usuario", [
            'user_id' => $userId,
            'subscriptions_found' => $subscriptions->count()
        ]);
        
        $successCount = 0;

        foreach ($subscriptions as $subscription) {
            Log::info("sendToUser: Intentando enviar a suscripción", [
                'subscription_id' => $subscription->id,
                'user_id' => $userId,
                'endpoint' => substr($subscription->endpoint, 0, 50) . '...'
            ]);
            
            if ($this->sendToSubscription($subscription, $payload)) {
                $successCount++;
                Log::info("sendToUser: Envío exitoso a suscripción", [
                    'subscription_id' => $subscription->id
                ]);
            } else {
                Log::warning("sendToUser: Envío fallido a suscripción", [
                    'subscription_id' => $subscription->id
                ]);
            }
        }

        Log::info("sendToUser: Resultado final", [
            'user_id' => $userId,
            'total_subscriptions' => $subscriptions->count(),
            'successful_sends' => $successCount
        ]);

        return $successCount;
    }

    /**
     * Enviar notificación a todos los usuarios suscritos
     */
    public function sendToAll(array $payload): int
    {
        $subscriptions = PushSubscription::all();
        $successCount = 0;

        Log::info("sendToAll: Enviando a todas las suscripciones", [
            'total_subscriptions' => $subscriptions->count()
        ]);

        foreach ($subscriptions as $subscription) {
            Log::info("sendToAll: Intentando enviar a suscripción", [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'is_anonymous' => is_null($subscription->user_id),
                'endpoint' => substr($subscription->endpoint, 0, 50) . '...'
            ]);

            if ($this->sendToSubscription($subscription, $payload)) {
                $successCount++;
                Log::info("sendToAll: Envío exitoso", [
                    'subscription_id' => $subscription->id
                ]);
            } else {
                Log::warning("sendToAll: Envío fallido", [
                    'subscription_id' => $subscription->id
                ]);
            }
        }

        Log::info("sendToAll: Resultado final", [
            'total_subscriptions' => $subscriptions->count(),
            'successful_sends' => $successCount
        ]);

        return $successCount;
    }

    /**
     * Crear una nueva suscripción push
     */
    public function createSubscription(?int $userId, array $subscriptionData): PushSubscription
    {
        $endpointHash = hash('sha256', $subscriptionData['endpoint']);
        
        Log::info('Creating/updating push subscription', [
            'user_id' => $userId,
            'endpoint_hash' => $endpointHash,
            'endpoint_preview' => substr($subscriptionData['endpoint'], 0, 50) . '...'
        ]);
        
        // Buscar si ya existe una suscripción con este endpoint
        $existingSubscription = PushSubscription::where('endpoint_hash', $endpointHash)->first();
        
        if ($existingSubscription) {
            Log::info('Found existing subscription, updating', [
                'existing_user_id' => $existingSubscription->user_id,
                'new_user_id' => $userId,
                'subscription_id' => $existingSubscription->id
            ]);
        } else {
            Log::info('Creating new subscription', [
                'user_id' => $userId,
                'endpoint_hash' => $endpointHash
            ]);
        }
        
        // Buscar por endpoint_hash únicamente, ya que un endpoint es único independientemente del usuario
        $subscription = PushSubscription::updateOrCreate(
            [
                'endpoint_hash' => $endpointHash,
            ],
            [
                'user_id' => $userId,
                'endpoint' => $subscriptionData['endpoint'],
                'public_key' => $subscriptionData['keys']['p256dh'],
                'auth_token' => $subscriptionData['keys']['auth'],
            ]
        );
        
        Log::info('Subscription created/updated successfully', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'was_recently_created' => $subscription->wasRecentlyCreated
        ]);
        
        return $subscription;
    }
}
