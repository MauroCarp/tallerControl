<?php
require_once 'vendor/autoload.php';

// Cargar el entorno de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Verificando configuración VAPID...\n\n";

$publicKey = config('app.vapid_public_key');
$privateKey = config('app.vapid_private_key');
$subject = config('app.vapid_subject');

echo "Public Key: " . ($publicKey ?: 'No configurada') . "\n";
echo "Private Key: " . ($privateKey ? str_repeat('*', 20) . substr($privateKey, -4) : 'No configurada') . "\n";
echo "Subject: " . ($subject ?: 'No configurado') . "\n\n";

if ($publicKey && $privateKey && $subject) {
    echo "✅ Todas las claves VAPID están configuradas\n";
    
    // Probar inicialización de WebPush
    try {
        $webPush = new \Minishlink\WebPush\WebPush([
            'VAPID' => [
                'subject' => $subject,
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ]);
        echo "✅ WebPush inicializado correctamente\n";
    } catch (Exception $e) {
        echo "❌ Error inicializando WebPush: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Faltan configuraciones VAPID\n";
}
?>
