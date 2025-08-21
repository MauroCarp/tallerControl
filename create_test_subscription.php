<?php
// Script para crear una suscripción de prueba

require_once 'vendor/autoload.php';

// Cargar el entorno de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PushSubscription;

echo "🔧 Creando suscripción de prueba...\n\n";

// Crear una suscripción mock para testing
$subscription = PushSubscription::create([
    'user_id' => 1, // Usuario ID 1
    'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint-' . time(),
    'public_key' => 'test-public-key-' . bin2hex(random_bytes(32)),
    'auth_token' => 'test-auth-token-' . bin2hex(random_bytes(16)),
]);

echo "✅ Suscripción de prueba creada:\n";
echo "   ID: {$subscription->id}\n";
echo "   Usuario ID: {$subscription->user_id}\n";
echo "   Endpoint: {$subscription->endpoint}\n\n";

echo "🚀 Ahora puedes probar enviando una notificación:\n";
echo "   php artisan push:send \"¡Prueba!\" \"Mensaje de prueba\" --user=1\n\n";

echo "📝 Para eliminar esta suscripción de prueba, ejecuta:\n";
echo "   php artisan tinker --execute=\"App\\Models\\PushSubscription::find({$subscription->id})->delete();\"\n";
?>
