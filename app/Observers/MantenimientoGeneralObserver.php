<?php

namespace App\Observers;

use App\Models\MantenimientoGeneral;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Log;

class MantenimientoGeneralObserver
{
    private PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Handle the MantenimientoGeneral "created" event.
     */
    public function created(MantenimientoGeneral $mantenimientoGeneral): void
    {
        try {
            // Enviar notificación al usuario ID 6
            $payload = [
                'title' => '🔧 Nuevo Mantenimiento General',
                'body' => "Se ha creado un nuevo registro de mantenimiento general #{$mantenimientoGeneral->id}",
                'icon' => '/images/icons/icon-192x192.png',
                'badge' => '/images/icons/icon-72x72.png',
                'tag' => 'mantenimiento-general-' . $mantenimientoGeneral->id,
                'data' => [
                    'url' => url('/admin/mantenimiento-generals/' . $mantenimientoGeneral->id),
                    'type' => 'mantenimiento_general',
                    'record_id' => $mantenimientoGeneral->id,
                    'timestamp' => now()->toISOString(),
                ]
            ];

            $successCount = $this->pushService->sendToUser(6, $payload);
            
            Log::info('Notificación automática enviada', [
                'type' => 'mantenimiento_general_created',
                'record_id' => $mantenimientoGeneral->id,
                'user_id' => 6,
                'notifications_sent' => $successCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando notificación automática', [
                'type' => 'mantenimiento_general_created',
                'record_id' => $mantenimientoGeneral->id,
                'user_id' => 6,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the MantenimientoGeneral "updated" event.
     */
    public function updated(MantenimientoGeneral $mantenimientoGeneral): void
    {
        // Verificar si el campo 'reparado' cambió a 1 (completado)
        if ($mantenimientoGeneral->wasChanged('reparado') && $mantenimientoGeneral->reparado == 1) {
            try {
                $payload = [
                    'title' => '✅ Mantenimiento Completado',
                    'body' => "El mantenimiento general #{$mantenimientoGeneral->id} ha sido marcado como completado",
                    'icon' => '/images/icons/icon-192x192.png',
                    'badge' => '/images/icons/icon-72x72.png',
                    'tag' => 'mantenimiento-completado-' . $mantenimientoGeneral->id,
                    'data' => [
                        'url' => url('/admin/mantenimiento-generals/' . $mantenimientoGeneral->id),
                        'type' => 'mantenimiento_general_completed',
                        'record_id' => $mantenimientoGeneral->id,
                        'timestamp' => now()->toISOString(),
                    ]
                ];

                $successCount = $this->pushService->sendToUser(6, $payload);
                
                Log::info('Notificación de completado enviada', [
                    'type' => 'mantenimiento_general_completed',
                    'record_id' => $mantenimientoGeneral->id,
                    'user_id' => 6,
                    'notifications_sent' => $successCount
                ]);

            } catch (\Exception $e) {
                Log::error('Error enviando notificación de completado', [
                    'type' => 'mantenimiento_general_completed',
                    'record_id' => $mantenimientoGeneral->id,
                    'user_id' => 6,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle the MantenimientoGeneral "deleted" event.
     */
    public function deleted(MantenimientoGeneral $mantenimientoGeneral): void
    {
        //
    }

    /**
     * Handle the MantenimientoGeneral "restored" event.
     */
    public function restored(MantenimientoGeneral $mantenimientoGeneral): void
    {
        //
    }

    /**
     * Handle the MantenimientoGeneral "force deleted" event.
     */
    public function forceDeleted(MantenimientoGeneral $mantenimientoGeneral): void
    {
        //
    }

    public function creating(MantenimientoGeneral $mantenimiento)
    {
        // Comentado temporalmente hasta verificar la estructura de la tabla
        // if ($mantenimiento->prioridad_orden && $mantenimiento->reparado == 0) {
        //     MantenimientoGeneral::reorganizarPrioridades($mantenimiento->prioridad_orden);
        // }
    }

    public function updating(MantenimientoGeneral $mantenimiento)
    {
        // Comentado temporalmente hasta verificar la estructura de la tabla
        // if ($mantenimiento->isDirty('prioridad_orden') && $mantenimiento->reparado == 0) {
        //     MantenimientoGeneral::reorganizarPrioridades(
        //         $mantenimiento->prioridad_orden, 
        //         $mantenimiento->id
        //     );
        // }
    }
}
