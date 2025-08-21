<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PushSubscription;
use App\Services\PushNotificationService;

class DiagnoseChromePush extends Command
{
    protected $signature = 'push:diagnose-chrome';
    protected $description = 'Diagnose Chrome push notification issues';

    public function handle()
    {
        $this->info('🔍 Diagnosticando problemas de Chrome con Push Notifications...');
        $this->line('');

        // Verificar suscripciones
        $subscriptions = PushSubscription::all();
        $this->info("📱 Total suscripciones: {$subscriptions->count()}");
        
        if ($subscriptions->count() === 0) {
            $this->error('❌ No hay suscripciones. Ve a http://127.0.0.1:8000/chrome-debug para crear una.');
            return 1;
        }

        foreach ($subscriptions as $subscription) {
            $endpointType = strpos($subscription->endpoint, 'fcm.googleapis.com') !== false ? 'Chrome/FCM' : 'Firefox/Mozilla';
            $this->line("  - ID: {$subscription->id}, Usuario: {$subscription->user_id}, Tipo: {$endpointType}");
            $this->line("    Endpoint: " . substr($subscription->endpoint, -40));
        }

        $this->line('');
        $this->info('🧪 Probando envío de notificación específica para Chrome...');

        // Enviar notificación de prueba con configuración específica para Chrome
        $pushService = app(PushNotificationService::class);
        
        $payload = [
            'title' => 'Prueba Chrome Específica',
            'body' => 'Esta notificación está optimizada para Chrome',
            'icon' => '/images/icons/icon-192x192.png',
            'badge' => '/images/icons/icon-72x72.png',
            'tag' => 'chrome-test-' . time(),
            'requireInteraction' => false,
            'silent' => false,
            'data' => [
                'url' => url('/'),
                'timestamp' => now()->toISOString(),
                'test' => true
            ]
        ];

        $totalSent = 0;
        foreach ($subscriptions as $subscription) {
            $result = $pushService->sendToSubscription($subscription, $payload);
            if ($result) {
                $totalSent++;
                $this->info("  ✅ Enviado a suscripción {$subscription->id}");
            } else {
                $this->error("  ❌ Falló envío a suscripción {$subscription->id}");
            }
        }

        $this->line('');
        $this->info("📊 Resultado: {$totalSent}/{$subscriptions->count()} notificaciones enviadas");

        if ($totalSent === 0) {
            $this->error('❌ No se enviaron notificaciones. Revisa los logs para más detalles.');
            $this->line('   Ejecuta: Get-Content -Path "storage/logs/laravel.log" -Tail 20');
        } else {
            $this->info('✅ Notificaciones enviadas. Si no aparecen en Chrome:');
            $this->line('   1. Verifica que las notificaciones estén habilitadas en Chrome');
            $this->line('   2. Abre las DevTools (F12) y revisa la consola');
            $this->line('   3. Ve a chrome://settings/content/notifications');
            $this->line('   4. Asegúrate de que el sitio tenga permisos');
        }

        return 0;
    }
}
