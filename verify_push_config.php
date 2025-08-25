<?php

echo "=== VERIFICACIÓN DE CONFIGURACIÓN PUSH NOTIFICATIONS ===\n\n";

// Verificar que estamos en el directorio correcto
if (!file_exists('artisan')) {
    echo "❌ Error: Este script debe ejecutarse desde la raíz del proyecto Laravel\n";
    exit(1);
}

require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "1. Verificando configuración VAPID...\n";

$publicKey = config('app.vapid_public_key');
$privateKey = config('app.vapid_private_key');
$subject = config('app.vapid_subject');

echo "   - VAPID_PUBLIC_KEY: " . ($publicKey ? "✅ CONFIGURADO" : "❌ NO CONFIGURADO") . "\n";
echo "   - VAPID_PRIVATE_KEY: " . ($privateKey ? "✅ CONFIGURADO" : "❌ NO CONFIGURADO") . "\n";
echo "   - VAPID_SUBJECT: " . ($subject ? "✅ CONFIGURADO ($subject)" : "❌ NO CONFIGURADO") . "\n\n";

if (!$publicKey || !$privateKey) {
    echo "❌ PROBLEMA ENCONTRADO: Las claves VAPID no están configuradas.\n\n";
    echo "SOLUCIÓN:\n";
    echo "1. Ejecuta: php generate_vapid.php\n";
    echo "2. Copia las claves generadas al archivo .env\n";
    echo "3. Ejecuta: php artisan config:cache\n\n";
} else {
    echo "✅ Configuración VAPID correcta\n\n";
    
    // Verificar que las claves sean válidas
    if (strlen($publicKey) >= 80 && strlen($privateKey) >= 40) {
        echo "✅ Las claves VAPID tienen el formato correcto\n\n";
    } else {
        echo "⚠️  Las claves VAPID parecen tener formato incorrecto\n";
        echo "   - Clave pública: " . strlen($publicKey) . " caracteres (esperado: ~87)\n";
        echo "   - Clave privada: " . strlen($privateKey) . " caracteres (esperado: ~43)\n\n";
    }
}

echo "2. Verificando rutas API...\n";

try {
    // Simular una request para obtener la clave VAPID
    $request = Illuminate\Http\Request::create('/api/push-notifications/vapid-public-key', 'GET');
    $response = app()->handle($request);
    
    if ($response->status() === 200) {
        echo "   - Ruta API /api/push-notifications/vapid-public-key: ✅ FUNCIONANDO\n";
        $data = json_decode($response->getContent(), true);
        if (isset($data['publicKey']) && $data['publicKey']) {
            echo "   - Respuesta contiene clave pública: ✅ SÍ\n";
        } else {
            echo "   - Respuesta contiene clave pública: ❌ NO\n";
        }
    } else {
        echo "   - Ruta API /api/push-notifications/vapid-public-key: ❌ ERROR (Status: " . $response->status() . ")\n";
    }
} catch (Exception $e) {
    echo "   - Error probando ruta API: ❌ " . $e->getMessage() . "\n";
}

echo "\n3. Verificando base de datos...\n";

try {
    $subscriptionsCount = \App\Models\PushSubscription::count();
    echo "   - Tabla push_subscriptions: ✅ ACCESIBLE\n";
    echo "   - Suscripciones existentes: $subscriptionsCount\n";
} catch (Exception $e) {
    echo "   - Tabla push_subscriptions: ❌ ERROR - " . $e->getMessage() . "\n";
}

echo "\n4. Verificando Service Worker...\n";

$serviceWorkerPath = public_path('serviceworker.js');
if (file_exists($serviceWorkerPath)) {
    echo "   - Service Worker (serviceworker.js): ✅ EXISTE\n";
} else {
    echo "   - Service Worker (serviceworker.js): ❌ NO ENCONTRADO\n";
}

echo "\n=== RESUMEN ===\n";

$issues = [];

if (!$publicKey || !$privateKey) {
    $issues[] = "Claves VAPID no configuradas";
}

if (!file_exists($serviceWorkerPath)) {
    $issues[] = "Service Worker no encontrado";
}

if (empty($issues)) {
    echo "✅ Todo parece estar configurado correctamente\n";
    echo "Si sigues teniendo problemas, verifica:\n";
    echo "- Que el archivo .env en producción tenga las claves VAPID\n";
    echo "- Que hayas ejecutado 'php artisan config:cache' después de cambiar el .env\n";
    echo "- Que tu servidor web esté sirviendo correctamente los archivos estáticos\n";
} else {
    echo "❌ Problemas encontrados:\n";
    foreach ($issues as $issue) {
        echo "   - $issue\n";
    }
}

echo "\n";
