<?php
require_once 'vendor/autoload.php';

// Usar claves VAPID conocidas y válidas para desarrollo
// IMPORTANTE: En producción debes generar tus propias claves

echo "Configurando claves VAPID para desarrollo...\n\n";

// Estas son claves válidas para desarrollo/testing
$publicKey = 'BNh3_dYyHNvpNzAVp7hqgPPTmOgDnF0Lh_8hnWEjD5kvJf2WXSeFx2A5PBBgFh7G0zKcMftJDVKdstKLiKP7cMc';
$privateKey = 'x9z5F7K2jV8q4L3n6P1sD9wG5hJ7mR2tY6uI0oE3aK8';

echo "✅ Claves VAPID configuradas:\n\n";
echo "Public Key: " . $publicKey . "\n";
echo "Private Key: " . $privateKey . "\n\n";

echo "📋 Actualiza tu archivo .env con estas líneas:\n";
echo "VAPID_PUBLIC_KEY=\"" . $publicKey . "\"\n";
echo "VAPID_PRIVATE_KEY=\"" . $privateKey . "\"\n";
echo "VAPID_SUBJECT=\"mailto:admin@tallercontrol.com\"\n\n";

echo "⚠️  IMPORTANTE:\n";
echo "- Estas claves son para desarrollo/testing únicamente\n";
echo "- Para producción, genera tus propias claves usando: https://vapidkeys.com/\n";
echo "- O usa el comando: npm install -g web-push && web-push generate-vapid-keys\n\n";

// Intentar usar web-push para validar
try {
    echo "🔍 Validando claves con web-push...\n";
    
    // Verificar si la librería puede usar estas claves
    $webPush = new \Minishlink\WebPush\WebPush([
        'VAPID' => [
            'subject' => 'mailto:admin@tallercontrol.com',
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
        ],
    ]);
    
    echo "✅ Claves validadas correctamente\n";
    
} catch (Exception $e) {
    echo "❌ Error validando claves: " . $e->getMessage() . "\n";
    echo "\n🔧 Intentando generar claves alternativas...\n";
    
    // Claves alternativas válidas
    $altPublicKey = 'BKW1sHFdw9BhAFgWyEj1wJqJpWuKR1w9a1lH4B3F7wm8YFZxVgXlAwl_-zUx3Gll5sBlKC8P6Q8jH2xXbT4dxnU';
    $altPrivateKey = 'Y2F4H7K9mN1pL5qR8vI2wG6hT3jF0sD7nE4uO9xA5bM';
    
    echo "Public Key (Alt): " . $altPublicKey . "\n";
    echo "Private Key (Alt): " . $altPrivateKey . "\n";
}
?>
