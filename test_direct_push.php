<?php

require_once 'vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

echo "Test directo de Web Push\n";

// VAPID keys (las mismas del .env)
$publicKey = 'BL84_CHtLTrbLGAVMQIgPXrRdepd-b34Mesjm6jAdSX7lg3bN6Lh4f6TUcX5VX8r3443IgAzOBbpvm3W35XVCZc';
$privateKey = 'oawYatke3AIwxhvgMno104dXaKmAp-DYY3CGRYChxWg';
$subject = 'mailto:admin@tallercontrol.com';

try {
    // Crear WebPush instance
    $webPush = new WebPush([
        'VAPID' => [
            'subject' => $subject,
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
        ],
    ]);
    
    echo "✅ WebPush instance created\n";
    
    // Obtener una suscripción de la base de datos
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=tallercontrol', 'root', '');
    $stmt = $pdo->query('SELECT * FROM push_subscriptions LIMIT 1');
    $sub = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$sub) {
        echo "❌ No hay suscripciones en la BD\n";
        exit;
    }
    
    echo "📡 Probando con suscripción ID: {$sub['id']}\n";
    echo "🔗 Endpoint: " . substr($sub['endpoint'], -30) . "\n";
    
    // Crear subscription object
    $subscription = Subscription::create([
        'endpoint' => $sub['endpoint'],
        'keys' => [
            'p256dh' => $sub['public_key'],
            'auth' => $sub['auth_token'],
        ],
    ]);
    
    echo "✅ Subscription object created\n";
    
    // Payload
    $payload = json_encode([
        'title' => 'Test directo',
        'body' => 'Esta es una prueba directa',
        'icon' => '/images/icons/icon-192x192.png',
    ]);
    
    echo "📤 Enviando notificación...\n";
    
    // Enviar
    $result = $webPush->sendOneNotification($subscription, $payload);
    
    if ($result->isSuccess()) {
        echo "✅ ¡Notificación enviada exitosamente!\n";
    } else {
        echo "❌ Error enviando notificación:\n";
        echo "Reason: " . $result->getReason() . "\n";
        if ($result->getResponse()) {
            echo "Status: " . $result->getResponse()->getStatusCode() . "\n";
            echo "Body: " . $result->getResponse()->getBody() . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
