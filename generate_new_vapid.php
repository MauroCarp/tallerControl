<?php
require_once 'vendor/autoload.php';

use Minishlink\WebPush\VAPID;

echo "🔑 Generando nuevas claves VAPID...\n\n";

try {
    // Generar claves VAPID reales
    $keys = VAPID::createVapidKeys();
    
    $publicKey = $keys['publicKey'];
    $privateKey = $keys['privateKey'];
    
    echo "✅ Claves VAPID generadas exitosamente:\n\n";
    echo "📋 Copia estas líneas a tu archivo .env:\n\n";
    echo "VAPID_PUBLIC_KEY=\"{$publicKey}\"\n";
    echo "VAPID_PRIVATE_KEY=\"{$privateKey}\"\n";
    echo "VAPID_SUBJECT=\"mailto:admin@tallercontrol.com\"\n\n";
    
    echo "📊 Información de las claves:\n";
    echo "Public Key Length: " . strlen($publicKey) . " caracteres\n";
    echo "Private Key Length: " . strlen($privateKey) . " caracteres\n\n";
    
    // Validar las claves generadas
    echo "🔍 Validando claves generadas...\n";
    $webPush = new \Minishlink\WebPush\WebPush([
        'VAPID' => [
            'subject' => 'mailto:admin@tallercontrol.com',
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
        ],
    ]);
    echo "✅ Claves validadas correctamente\n\n";
    
    echo "🎉 ¡Listo! Copia las líneas del .env y reinicia el servidor.\n";
    
} catch (Exception $e) {
    echo "❌ Error generando claves: " . $e->getMessage() . "\n";
    exit(1);
}
?>
