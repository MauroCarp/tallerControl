<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MantenimientoGeneral;
use App\Models\User;
use App\Models\PushSubscription;
use App\Services\PushNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Log;
use Mockery;

class MantenimientoGeneralPushNotificationTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    protected $mockPushService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock del servicio de push notifications
        $this->mockPushService = Mockery::mock(PushNotificationService::class);
        $this->app->instance(PushNotificationService::class, $this->mockPushService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test que verifica que se envíe una push notification al crear un MantenimientoGeneral
     */
    public function test_push_notification_sent_when_mantenimiento_general_is_created()
    {
        // Arrange: Crear usuario con ID 6
        $usuario = User::factory()->create(['id' => 6]);
        
        // Crear una suscripción push para el usuario
        PushSubscription::create([
            'user_id' => 6,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/example-endpoint-' . uniqid(),
            'public_key' => 'test-public-key-' . uniqid(),
            'auth_token' => 'test-auth-token-' . uniqid(),
        ]);

        // Configurar el mock para esperar que se llame sendToUser con usuario ID 6
        $this->mockPushService
            ->shouldReceive('sendToUser')
            ->once()
            ->with(6, Mockery::on(function ($payload) {
                // Verificar que el payload contiene los campos esperados
                return isset($payload['title']) &&
                       isset($payload['body']) &&
                       isset($payload['icon']) &&
                       isset($payload['data']) &&
                       $payload['title'] === '🔧 Nuevo Tarea de Mantenimiento' &&
                       str_contains($payload['body'], 'Se ha creado una nueva tarea de mantenimiento') &&
                       $payload['data']['type'] === 'mantenimiento_general' &&
                       isset($payload['data']['record_id']);
            }))
            ->andReturn(1); // Simular que se envió 1 notificación exitosamente

        // Act: Crear un nuevo MantenimientoGeneral (esto debería disparar el observer)
        // Solo campos del primer paso del formulario (SOLICITUD)
        $mantenimiento = MantenimientoGeneral::create([
            'fechaSolicitud' => now()->format('Y-m-d'),
            'tarea' => 'Revisión general del sistema de frenos',
            'prioridad' => 'ALTA',
            'solicitado' => 'Mantenimiento',
        ]);

        // Assert: Verificar que el mantenimiento se creó correctamente
        $this->assertInstanceOf(MantenimientoGeneral::class, $mantenimiento);
        $this->assertDatabaseHas('mantenimiento_general', [
            'id' => $mantenimiento->id,
            'tarea' => 'Revisión general del sistema de frenos',
            'solicitado' => 'Revisión completa de pastillas y discos',
            'reparado' => false,
        ]);

        // Verificar que el mock fue llamado correctamente (se verifica automáticamente en tearDown)
    }

    /**
     * Test que verifica el comportamiento cuando no hay suscripciones para el usuario
     */
    public function test_push_notification_handles_user_without_subscriptions()
    {
        // Arrange: Crear usuario con ID 6 pero SIN suscripciones push
        $usuario = User::factory()->create(['id' => 6]);

        // Configurar el mock para retornar 0 (no se enviaron notificaciones)
        $this->mockPushService
            ->shouldReceive('sendToUser')
            ->once()
            ->with(6, Mockery::any())
            ->andReturn(0); // No se envió ninguna notificación

        // Act: Crear un nuevo MantenimientoGeneral
        // Solo campos del primer paso del formulario (SOLICITUD)
        $mantenimiento = MantenimientoGeneral::create([
            'fechaSolicitud' => now()->format('Y-m-d'),
            'tarea' => 'Cambio de aceite y filtros',
            'prioridad' => 'NORMAL',
            'solicitado' => 'Produccion',
        ]);

        // Assert: Verificar que el mantenimiento se creó a pesar de no tener suscripciones
        $this->assertInstanceOf(MantenimientoGeneral::class, $mantenimiento);
        $this->assertDatabaseHas('mantenimiento_general', [
            'id' => $mantenimiento->id,
            'tarea' => 'Cambio de aceite y filtros',
        ]);
    }

    /**
     * Test que verifica que se manejen correctamente los errores en el envío de notificaciones
     */
    public function test_push_notification_handles_service_errors_gracefully()
    {
        // Arrange: Crear usuario con ID 6
        $usuario = User::factory()->create(['id' => 6]);

        // Configurar el mock para lanzar una excepción
        $this->mockPushService
            ->shouldReceive('sendToUser')
            ->once()
            ->with(6, Mockery::any())
            ->andThrow(new \Exception('Error simulado del servicio push'));

        // Act & Assert: Crear el mantenimiento no debería fallar aunque el servicio push falle
        // Solo campos del primer paso del formulario (SOLICITUD)
        $mantenimiento = MantenimientoGeneral::create([
            'fechaSolicitud' => now()->format('Y-m-d'),
            'tarea' => 'Diagnóstico eléctrico',
            'prioridad' => 'MUY ALTA',
            'solicitado' => 'Gerencial',
        ]);

        // Verificar que el mantenimiento se creó exitosamente a pesar del error
        $this->assertInstanceOf(MantenimientoGeneral::class, $mantenimiento);
        $this->assertDatabaseHas('mantenimiento_general', [
            'id' => $mantenimiento->id,
            'tarea' => 'Diagnóstico eléctrico',
        ]);
    }

    /**
     * Test que verifica el contenido específico del payload de la notificación
     */
    public function test_push_notification_payload_contains_correct_data()
    {
        // Arrange
        $usuario = User::factory()->create(['id' => 6]);
        
        PushSubscription::create([
            'user_id' => 6,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
            'public_key' => 'test-public-key',
            'auth_token' => 'test-auth-token',
        ]);

        $expectedSolicitado = 'Administracion';

        // Configurar el mock con verificaciones más específicas
        $this->mockPushService
            ->shouldReceive('sendToUser')
            ->once()
            ->with(6, Mockery::on(function ($payload) use ($expectedSolicitado) {
                // Verificaciones detalladas del payload
                $this->assertEquals('🔧 Nuevo Tarea de Mantenimiento', $payload['title']);
                $this->assertStringContainsString($expectedSolicitado, $payload['body']);
                $this->assertEquals('/images/icons/icon-192x192.png', $payload['icon']);
                $this->assertEquals('/images/icons/icon-72x72.png', $payload['badge']);
                $this->assertArrayHasKey('tag', $payload);
                $this->assertArrayHasKey('vibrate', $payload);
                $this->assertEquals([200, 100, 200], $payload['vibrate']);
                
                // Verificar estructura de data
                $this->assertArrayHasKey('data', $payload);
                $this->assertArrayHasKey('url', $payload['data']);
                $this->assertArrayHasKey('type', $payload['data']);
                $this->assertArrayHasKey('record_id', $payload['data']);
                $this->assertArrayHasKey('timestamp', $payload['data']);
                
                $this->assertEquals('mantenimiento_general', $payload['data']['type']);
                $this->assertStringContainsString('/mantenimiento-generals', $payload['data']['url']);
                
                return true;
            }))
            ->andReturn(1);

        // Act
        // Solo campos del primer paso del formulario (SOLICITUD)
        $mantenimiento = MantenimientoGeneral::create([
            'fechaSolicitud' => now()->format('Y-m-d'),
            'tarea' => 'Mantenimiento preventivo',
            'prioridad' => 'ALTA',
            'solicitado' => 'Administracion',
        ]);

        // Assert: Las verificaciones se hacen en el callback del mock
        $this->assertTrue(true); // Test pasa si no hay excepciones en las verificaciones del mock
    }

    /**
     * Test para verificar que el observer está correctamente registrado
     */
    public function test_observer_is_registered()
    {
        // Verificar que el observer está registrado comprobando que el evento se dispara
        $eventFired = false;
        
        // Escuchar el evento de logging para verificar que el observer funciona
        Log::shouldReceive('info')
            ->withArgs(function ($message, $context) use (&$eventFired) {
                if ($message === 'Notificación automática enviada' && 
                    isset($context['type']) && 
                    $context['type'] === 'mantenimiento_general_created') {
                    $eventFired = true;
                    return true;
                }
                return true; // Permitir otros logs
            })
            ->zeroOrMoreTimes();

        // Mock del servicio
        $this->mockPushService
            ->shouldReceive('sendToUser')
            ->once()
            ->andReturn(1);

        // Crear usuario y mantenimiento
        User::factory()->create(['id' => 6]);
        
        // Solo campos del primer paso del formulario (SOLICITUD)
        MantenimientoGeneral::create([
            'fechaSolicitud' => now()->format('Y-m-d'),
            'tarea' => 'Test observer registration',
            'prioridad' => 'BAJA',
            'solicitado' => 'Mantenimiento',
        ]);

        // Verificar que el evento se disparó
        $this->assertTrue($eventFired, 'El observer no se ejecutó correctamente');
    }

    /**
     * Test completo del escenario solicitado:
     * Crear un registro de mantenimiento general que complete el primer paso del formulario
     * y verificar que se envía notificación push al usuario ID 6
     */
    public function test_complete_first_step_mantenimiento_general_sends_push_notification_to_user_6()
    {
        // Arrange: Preparar el escenario
        
        // 1. Crear el usuario Carlos Morelli (ID 6) que debería recibir la notificación
        $carlosMorelli = User::factory()->create([
            'id' => 6,
            'name' => 'Carlos Morelli',
            'email' => 'carlos.morelli@tallercontrol.com'
        ]);
        
        // 2. Crear una suscripción push activa para Carlos
        $suscripcionCarlos = PushSubscription::create([
            'user_id' => 6,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/carlos-endpoint-' . uniqid(),
            'public_key' => 'carlos-public-key-' . uniqid(),
            'auth_token' => 'carlos-auth-token-' . uniqid(),
        ]);

        // 3. Configurar el mock del servicio push para verificar que se llama correctamente
        $payloadVerified = false;
        $this->mockPushService
            ->shouldReceive('sendToUser')
            ->once()
            ->with(6, Mockery::on(function ($payload) use (&$payloadVerified) {
                // Verificar que el payload contiene toda la información esperada
                $this->assertIsArray($payload);
                $this->assertEquals('🔧 Nuevo Tarea de Mantenimiento', $payload['title']);
                $this->assertStringContainsString('Se ha creado una nueva tarea de mantenimiento', $payload['body']);
                $this->assertStringContainsString('Instalación de sistema de seguridad completo', $payload['body']);
                $this->assertEquals('/images/icons/icon-192x192.png', $payload['icon']);
                $this->assertEquals('/images/icons/icon-72x72.png', $payload['badge']);
                $this->assertArrayHasKey('tag', $payload);
                $this->assertStringContainsString('mantenimiento-general-', $payload['tag']);
                $this->assertEquals([200, 100, 200], $payload['vibrate']);
                
                // Verificar estructura de datos adicionales
                $this->assertArrayHasKey('data', $payload);
                $this->assertEquals('mantenimiento_general', $payload['data']['type']);
                $this->assertStringContainsString('/mantenimiento-generals', $payload['data']['url']);
                $this->assertArrayHasKey('record_id', $payload['data']);
                $this->assertArrayHasKey('timestamp', $payload['data']);
                
                $payloadVerified = true;
                return true;
            }))
            ->andReturn(1); // Simular envío exitoso

        // 4. Mock del sistema de logging para verificar que se registra correctamente
        $logRecorded = false;
        Log::shouldReceive('info')
            ->withArgs(function ($message, $context) use (&$logRecorded) {
                if ($message === 'Notificación automática enviada' && 
                    $context['type'] === 'mantenimiento_general_created' &&
                    $context['user_id'] === 6 &&
                    $context['notifications_sent'] === 1) {
                    $logRecorded = true;
                }
                return true;
            })
            ->zeroOrMoreTimes();

        // Act: Simular el llenado completo del primer paso del formulario de mantenimiento general
        $datosFormularioPrimerPaso = [
            'fechaSolicitud' => '2025-08-25',
            'tarea' => 'Instalación de sistema de seguridad',
            'prioridad' => 'MUY ALTA',
            'solicitado' => 'Gerencial',
        ];

        // Crear el registro (esto debería disparar el observer automáticamente)
        $mantenimientoCreado = MantenimientoGeneral::create($datosFormularioPrimerPaso);

        // Assert: Verificar que todo funcionó correctamente
        
        // 1. Verificar que el registro se creó correctamente en la base de datos
        $this->assertInstanceOf(MantenimientoGeneral::class, $mantenimientoCreado);
        $this->assertDatabaseHas('mantenimiento_generals', [
            'id' => $mantenimientoCreado->id,
            'tarea' => 'Instalación de sistema de seguridad',
            'prioridad' => 'MUY ALTA',
            'solicitado' => 'Gerencial',
        ]);

        // 2. Verificar que el usuario Carlos Morelli existe
        $this->assertDatabaseHas('users', [
            'id' => 6,
            'name' => 'Carlos Morelli',
            'email' => 'carlos.morelli@tallercontrol.com'
        ]);

        // 3. Verificar que la suscripción push existe
        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => 6,
            'endpoint' => $suscripcionCarlos->endpoint,
        ]);

        // 4. Verificar que el payload fue validado correctamente
        $this->assertTrue($payloadVerified, 'El payload de la notificación no pasó todas las validaciones');

        // 5. Verificar que se registró correctamente en los logs
        $this->assertTrue($logRecorded, 'La notificación no se registró correctamente en los logs');

        // 6. Verificar que los mocks se llamaron como se esperaba (automático con Mockery)
        
        // Test exitoso: Se creó el mantenimiento y se envió la notificación a Carlos Morelli (ID 6)
        $this->assertTrue(true, 'Test completado exitosamente: Mantenimiento creado y notificación enviada');
    }
}
