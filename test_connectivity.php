<?php
// Test simple de conectividad
echo "Servidor web funcionando correctamente\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version: " . phpversion() . "\n";

// Test de conexión a base de datos
try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $users = \App\Models\User::count();
    $mantenimientos = \App\Models\MantenimientoGeneral::count();
    $suscripciones = \App\Models\PushSubscription::count();
    
    echo "Conexión a BD: OK\n";
    echo "Usuarios: $users\n";
    echo "Mantenimientos: $mantenimientos\n";
    echo "Suscripciones push: $suscripciones\n";
    
} catch (Exception $e) {
    echo "Error en BD: " . $e->getMessage() . "\n";
}
?>
