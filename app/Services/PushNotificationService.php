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
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => config('app.vapid_subject'),
                'publicKey' => config('app.vapid_public_key'),
                'privateKey' => config('app.vapid_private_key'),
            ],
        ]);
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
        $successCount = 0;

        foreach ($subscriptions as $subscription) {
            if ($this->sendToSubscription($subscription, $payload)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Enviar notificación a todos los usuarios suscritos
     */
    public function sendToAll(array $payload): int
    {
        $subscriptions = PushSubscription::all();
        $successCount = 0;

        foreach ($subscriptions as $subscription) {
            if ($this->sendToSubscription($subscription, $payload)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Crear una nueva suscripción
     */
    public function createSubscription(int $userId, array $subscriptionData): PushSubscription
    {
        $endpointHash = hash('sha256', $subscriptionData['endpoint']);
        
        return PushSubscription::updateOrCreate(
            [
                'user_id' => $userId,
                'endpoint_hash' => $endpointHash,
            ],
            [
                'endpoint' => $subscriptionData['endpoint'],
                'public_key' => $subscriptionData['keys']['p256dh'],
                'auth_token' => $subscriptionData['keys']['auth'],
            ]
        );
    }
}
