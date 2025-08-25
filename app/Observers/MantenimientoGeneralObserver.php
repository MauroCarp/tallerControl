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
            'title' => '🔧 Nuevo Tarea de Mantenimiento',
            'body' => "Se ha creado una nueva tarea de mantenimiento {$mantenimientoGeneral->solicitado}",
            'icon' => '/images/icons/icon-192x192.png',
            'badge' => '/images/icons/icon-72x72.png',
            'tag' => 'mantenimiento-general-' . $mantenimientoGeneral->id,
            'vibrate' => [200, 100, 200], // ejemplo de patrón de vibración
            'data' => [
                'url' => url('/mantenimientoGeneral/mantenimiento-generals'),
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
        // Verificar si los campos prioridad_orden o fechaRealizar fueron modificados
        if ($mantenimientoGeneral->wasChanged('prioridad_orden') || $mantenimientoGeneral->wasChanged('fechaRealizar')) {
            try {
                $changedFields = [];
                if ($mantenimientoGeneral->wasChanged('prioridad_orden')) {
                    $changedFields[] = 'orden de prioridad';
                }
                if ($mantenimientoGeneral->wasChanged('fechaRealizar')) {
                    $changedFields[] = 'fecha a realizar';
                }
                
                $fieldsText = implode(' y ', $changedFields);
                
                $payload = [
                    'title' => '📋 Tarea de Mantenimiento Asignada',
                    'body' => "Se ha actualizado el {$fieldsText}. Tienes una nueva tarea por realizar.",
                    'icon' => '/images/icons/icon-192x192.png',
                    'badge' => '/images/icons/icon-72x72.png',
                    'tag' => 'mantenimiento-tarea-' . $mantenimientoGeneral->id,
                    'vibrate' => [200, 100, 200], // ejemplo de patrón de vibración
                    'data' => [
                        'url' => url('/mantenimientoGeneral/mantenimiento-generals/'),
                        'type' => 'mantenimiento_general_task_assigned',
                        'record_id' => $mantenimientoGeneral->id,
                        'changed_fields' => $changedFields,
                        'timestamp' => now()->toISOString(),
                    ]
                ];

                // Enviar notificación a los usuarios ID 4 y 5
                $userIds = [4, 5];
                $totalNotifications = 0;
                
                foreach ($userIds as $userId) {
                    $successCount = $this->pushService->sendToUser($userId, $payload);
                    $totalNotifications += $successCount;
                }
                
                Log::info('Notificaciones de tarea enviadas', [
                    'type' => 'mantenimiento_general_task_assigned',
                    'record_id' => $mantenimientoGeneral->id,
                    'user_ids' => $userIds,
                    'changed_fields' => $changedFields,
                    'notifications_sent' => $totalNotifications
                ]);

            } catch (\Exception $e) {
                Log::error('Error enviando notificaciones de tarea', [
                    'type' => 'mantenimiento_general_task_assigned',
                    'record_id' => $mantenimientoGeneral->id,
                    'user_ids' => [4, 5],
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Verificar si el campo 'reparado' cambió a 1 (completado)
        // if ($mantenimientoGeneral->wasChanged('reparado') && $mantenimientoGeneral->reparado == 1) {
        //     try {
        //         $payload = [
        //             'title' => '✅ Mantenimiento Completado',
        //             'body' => "El mantenimiento general #{$mantenimientoGeneral->id} ha sido marcado como completado",
        //             'icon' => '/images/icons/icon-192x192.png',
        //             'badge' => '/images/icons/icon-72x72.png',
        //             'tag' => 'mantenimiento-completado-' . $mantenimientoGeneral->id,
        //             'data' => [
        //                 'url' => url('/admin/mantenimiento-generals/' . $mantenimientoGeneral->id),
        //                 'type' => 'mantenimiento_general_completed',
        //                 'record_id' => $mantenimientoGeneral->id,
        //                 'timestamp' => now()->toISOString(),
        //             ]
        //         ];

        //         $successCount = $this->pushService->sendToUser(6, $payload);
                
        //         Log::info('Notificación de completado enviada', [
        //             'type' => 'mantenimiento_general_completed',
        //             'record_id' => $mantenimientoGeneral->id,
        //             'user_id' => 6,
        //             'notifications_sent' => $successCount
        //         ]);

        //     } catch (\Exception $e) {
        //         Log::error('Error enviando notificación de completado', [
        //             'type' => 'mantenimiento_general_completed',
        //             'record_id' => $mantenimientoGeneral->id,
        //             'user_id' => 6,
        //             'error' => $e->getMessage()
        //         ]);
        //     }
        // }
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
