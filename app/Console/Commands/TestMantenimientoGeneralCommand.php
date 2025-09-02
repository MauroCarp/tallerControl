<?php

namespace App\Console\Commands;

use App\Models\MantenimientoGeneral;
use App\Models\User;
use App\Models\PushSubscription;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestMantenimientoGeneralCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:mantenimiento-general 
                           {--tipo=completo : Tipo de mantenimiento (completo, frenos, motor, electrico)}
                           {--crear-usuario : Crear usuario ID 6 si no existe}
                           {--crear-suscripcion : Crear suscripción push de prueba}
                           {--limpiar : Limpiar datos de prueba}';

    /**
     * The console command description.
     */
    protected $description = 'Test manual del sistema de push notifications para MantenimientoGeneral';

    private PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        parent::__construct();
        $this->pushService = $pushService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Test de Mantenimiento General - Push Notifications');
        $this->info('====================================================');

        // Verificar si se quiere limpiar datos
        if ($this->option('limpiar')) {
            return $this->limpiarDatos();
        }

        // Verificar estado inicial
        $this->mostrarEstado();

        // Crear usuario si se solicita
        if ($this->option('crear-usuario')) {
            $this->crearUsuario6();
        }

        // Crear suscripción si se solicita
        if ($this->option('crear-suscripcion')) {
            $this->crearSuscripcion();
        }

        // Ejecutar el test principal
        $this->ejecutarTest();

        $this->info('');
        $this->info('✅ Test completado. Revisa los logs para más detalles.');
    }

    private function mostrarEstado()
    {
        $this->info('');
        $this->info('📊 Estado actual del sistema:');
        $this->line('────────────────────────────────');

        // Usuario ID 6
        $usuario6 = User::find(6);
        if ($usuario6) {
            $this->info("👤 Usuario ID 6: ✅ {$usuario6->name} ({$usuario6->email})");
        } else {
            $this->warn('👤 Usuario ID 6: ❌ No existe');
        }

        // Suscripciones push
        $suscripciones = PushSubscription::where('user_id', 6)->count();
        if ($suscripciones > 0) {
            $this->info("🔔 Suscripciones push: ✅ {$suscripciones} activas");
        } else {
            $this->warn('🔔 Suscripciones push: ❌ Ninguna activa');
        }

        // Mantenimientos
        $mantenimientos = MantenimientoGeneral::count();
        $this->info("🔧 Total mantenimientos: {$mantenimientos}");
    }

    private function crearUsuario6()
    {
        $this->info('');
        $this->info('👤 Creando usuario Carlos Morelli (ID 6)...');

        $usuario = User::find(6);
        if ($usuario) {
            $this->warn("Usuario ID 6 ya existe: {$usuario->name}");
            return;
        }

        try {
            $usuario = new User();
            $usuario->id = 6;
            $usuario->name = 'Carlos Morelli';
            $usuario->email = 'carlos.morelli@tallercontrol.com';
            $usuario->password = bcrypt('password123');
            $usuario->email_verified_at = now();
            $usuario->save();

            $this->info("✅ Usuario creado exitosamente: {$usuario->name}");
        } catch (\Exception $e) {
            $this->error("❌ Error creando usuario: {$e->getMessage()}");
        }
    }

    private function crearSuscripcion()
    {
        $this->info('');
        $this->info('🔔 Creando suscripción push de prueba...');

        $usuario6 = User::find(6);
        if (!$usuario6) {
            $this->error('❌ Primero debes crear el usuario ID 6 con --crear-usuario');
            return;
        }

        try {
            $suscripcion = PushSubscription::create([
                'user_id' => 6,
                'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-console-' . uniqid(),
                'public_key' => 'console-public-key-' . uniqid(),
                'auth_token' => 'console-auth-token-' . uniqid(),
            ]);

            $this->info("✅ Suscripción creada con ID: {$suscripcion->id}");
        } catch (\Exception $e) {
            $this->error("❌ Error creando suscripción: {$e->getMessage()}");
        }
    }

    private function ejecutarTest()
    {
        $tipo = $this->option('tipo');
        
        $this->info('');
        $this->info("🚀 Ejecutando test de mantenimiento tipo: {$tipo}");
        $this->line('────────────────────────────────────────────────');

        // Verificar prerequisites
        $usuario6 = User::find(6);
        if (!$usuario6) {
            $this->error('❌ Usuario ID 6 no existe. Usa --crear-usuario para crearlo.');
            return;
        }

        $suscripciones = PushSubscription::where('user_id', 6)->count();
        if ($suscripciones === 0) {
            $this->warn('⚠️ Usuario ID 6 no tiene suscripciones push. Usa --crear-suscripcion');
        }

        // Crear el mantenimiento
        try {
            $datos = $this->obtenerDatosMantenimiento($tipo);
            
            $this->info('📝 Datos del mantenimiento (Primer paso - SOLICITUD):');
            $this->line("   Tarea: {$datos['tarea']}");
            $this->line("   Prioridad: {$datos['prioridad']}");
            $this->line("   Solicitado por: {$datos['solicitado']}");

            // Esta línea disparará automáticamente el observer
            $mantenimiento = MantenimientoGeneral::create($datos);

            $this->info('');
            $this->info("✅ Mantenimiento creado con ID: {$mantenimiento->id}");
            $this->info("🔧 Observer ejecutado automáticamente");
            
            if ($suscripciones > 0) {
                $this->info("📱 Push notification enviada al usuario ID 6");
                $this->info("   (Revisar logs para confirmación)");
            } else {
                $this->warn("⚠️ No se envió push notification (sin suscripciones)");
            }

        } catch (\Exception $e) {
            $this->error("❌ Error creando mantenimiento: {$e->getMessage()}");
            Log::error('Error en test de consola', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function limpiarDatos()
    {
        $this->info('🗑️ Limpiando datos de prueba...');

        if (!$this->confirm('¿Estás seguro de que quieres eliminar los datos de prueba?')) {
            $this->info('Operación cancelada.');
            return;
        }

        try {
            // Eliminar suscripciones del usuario 6
            $suscripcionesEliminadas = PushSubscription::where('user_id', 6)->delete();

            // Eliminar últimos 10 mantenimientos
            $mantenimientos = MantenimientoGeneral::orderBy('created_at', 'desc')->take(10)->get();
            $mantenimientosEliminados = 0;
            foreach ($mantenimientos as $mantenimiento) {
                $mantenimiento->delete();
                $mantenimientosEliminados++;
            }

            $this->info("✅ Datos limpiados exitosamente:");
            $this->line("   Suscripciones eliminadas: {$suscripcionesEliminadas}");
            $this->line("   Mantenimientos eliminados: {$mantenimientosEliminados}");

        } catch (\Exception $e) {
            $this->error("❌ Error limpiando datos: {$e->getMessage()}");
        }
    }

    private function obtenerDatosMantenimiento(string $tipo): array
    {
        $base = [
            'fechaSolicitud' => now()->format('Y-m-d'),
            'solicitado' => 'Mantenimiento', // Simular que viene del sector Mantenimiento
        ];

        switch ($tipo) {
            case 'frenos':
                return array_merge($base, [
                    'tarea' => 'Test: Revisión sistema de frenos',
                    'prioridad' => 'ALTA',
                ]);
            
            case 'motor':
                return array_merge($base, [
                    'tarea' => 'Test: Mantenimiento preventivo motor',
                    'prioridad' => 'NORMAL',
                ]);
            
            case 'electrico':
                return array_merge($base, [
                    'tarea' => 'Test: Diagnóstico eléctrico',
                    'prioridad' => 'MUY ALTA',
                ]);
            
            default: // 'completo'
                return array_merge($base, [
                    'tarea' => 'Test: Mantenimiento integral completo',
                    'prioridad' => 'ALTA',
                ]);
        }
    }
}
