<?php

namespace Tests\Feature;

use App\Models\MantenimientoGeneral;
use App\Models\User;
use App\Models\PushSubscription;
use App\Services\PushNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Mockery;

class MantenimientoGeneralObserverTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario de prueba (ID 6 como en el observer)
        User::factory()->create([
            'id' => 6,
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function observer_sends_notification_when_mantenimiento_general_is_created()
    {
        // Mockear el servicio de push notifications
        $mockPushService = Mockery::mock(PushNotificationService::class);
        $mockPushService->shouldReceive('sendToUser')
            ->once()
            ->with(6, Mockery::type('array'))
            ->andReturn(1); // Simula que se envió 1 notificación

        $this->app->instance(PushNotificationService::class, $mockPushService);

        // Crear una suscripción push para el usuario
        PushSubscription::create([
            'user_id' => 6,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
            'p256dh' => 'test-p256dh-key',
            'auth' => 'test-auth-key'
        ]);

        // Capturar logs
        Log::shouldReceive('info')
            ->once()
            ->with('Notificación automática enviada', Mockery::type('array'));

        // Crear un nuevo registro de MantenimientoGeneral (solo primer step)
        $mantenimiento = MantenimientoGeneral::create([
            'fechaSolicitud' => now()->format('Y-m-d'),
            'tarea' => 'Revisión de motor - Test',
            'solicitado' => 'Producción',
            'reparado' => 0, // No reparado (primer step)
            'horas' => 0,
            'materiales' => '',
            'costo' => 0.0,
            'realizado' => '',
            'fechaRealizado' => null
        ]);

        // Verificar que el registro se creó correctamente
        $this->assertDatabaseHas('mantenimiento_general', [
            'id' => $mantenimiento->id,
            'tarea' => 'Revisión de motor - Test',
            'solicitado' => 'Producción',
            'reparado' => 0
        ]);

        // El mock verificará automáticamente que sendToUser fue llamado
        $this->assertTrue(true);
    }

    /** @test */
    public function observer_logs_error_when_notification_fails()
    {
        // Mockear el servicio para que lance una excepción
        $mockPushService = Mockery::mock(PushNotificationService::class);
        $mockPushService->shouldReceive('sendToUser')
            ->once()
            ->andThrow(new \Exception('Error de conexión'));

        $this->app->instance(PushNotificationService::class, $mockPushService);

        // Capturar log de error
        Log::shouldReceive('error')
            ->once()
            ->with('Error enviando notificación automática', Mockery::type('array'));

        // Crear registro que debería activar el observer
        MantenimientoGeneral::create([
            'fechaSolicitud' => now()->format('Y-m-d'),
            'tarea' => 'Test con error',
            'solicitado' => 'Administración',
            'reparado' => 0,
            'horas' => 0,
            'materiales' => '',
            'costo' => 0.0,
            'realizado' => '',
            'fechaRealizado' => null
        ]);

        $this->assertTrue(true);
    }

    /** @test */
    public function observer_sends_completion_notification_when_reparado_changes_to_1()
    {
        // Crear un registro existente
        $mantenimiento = MantenimientoGeneral::create([
            'fechaSolicitud' => now()->format('Y-m-d'),
            'tarea' => 'Test completado',
            'solicitado' => 'Mantenimiento',
            'reparado' => 0,
            'horas' => 0,
            'materiales' => '',
            'costo' => 0.0,
            'realizado' => '',
            'fechaRealizado' => null
        ]);

        // Mockear el servicio para la notificación de completado
        $mockPushService = Mockery::mock(PushNotificationService::class);
        $mockPushService->shouldReceive('sendToUser')
            ->once()
            ->with(6, Mockery::on(function ($payload) {
                return $payload['title'] === '✅ Mantenimiento Completado';
            }))
            ->andReturn(1);

        $this->app->instance(PushNotificationService::class, $mockPushService);

        // Capturar log de completado
        Log::shouldReceive('info')
            ->once()
            ->with('Notificación de completado enviada', Mockery::type('array'));

        // Actualizar el registro para marcarlo como completado
        $mantenimiento->update([
            'reparado' => 1,
            'realizado' => 'Carlos Mecánico',
            'fechaRealizado' => now()->format('Y-m-d'),
            'horas' => 3,
            'materiales' => 'Aceite, filtros',
            'costo' => 15000.00
        ]);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
