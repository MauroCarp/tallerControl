<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PushNotificationService;
use App\Models\PushSubscription;

class TestPushNotification extends Command
{
    protected $signature = 'push:test';
    protected $description = 'Test push notification sending';

    public function handle()
    {
        $pushService = app(PushNotificationService::class);
        
        $this->info('Testing push notification...');
        
        $subscriptions = PushSubscription::all();
        $this->line("Found {$subscriptions->count()} subscriptions");
        
        foreach ($subscriptions as $subscription) {
            $this->line("Testing subscription ID: {$subscription->id}");
            $this->line("Endpoint: " . substr($subscription->endpoint, -30));
            
            $payload = [
                'title' => 'Test desde consola',
                'body' => 'Esta es una notificación de prueba',
                'icon' => '/images/icons/icon-192x192.png',
            ];
            
            $result = $pushService->sendToSubscription($subscription, $payload);
            
            if ($result) {
                $this->info("✅ Enviado exitosamente a suscripción {$subscription->id}");
            } else {
                $this->error("❌ Falló el envío a suscripción {$subscription->id}");
            }
        }

        return 0;
    }
}
