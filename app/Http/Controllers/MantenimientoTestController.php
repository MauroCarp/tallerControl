<?php

namespace App\Http\Controllers;

use App\Models\MantenimientoGeneral;
use App\Models\User;
use App\Models\PushSubscription;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MantenimientoTestController extends Controller
{
    private PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    public function index()
    {
        return view('mantenimiento-test');
    }

    public function createTestMantenimiento(Request $request)
    {
        try {
            // Limpiar logs anteriores para el test
            Log::info('=== INICIO DEL TEST DE MANTENIMIENTO GENERAL ===');

            // Verificar que existe el usuario ID 6
            $user = User::find(6);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario ID 6 no encontrado. El observer está configurado para enviar notificaciones al usuario ID 6.',
                    'user_exists' => false
                ], 400);
            }

            // Verificar suscripciones push del usuario
            $subscriptionsCount = PushSubscription::where('user_id', 6)->count();
            
            Log::info('Estado antes del test:', [
                'user_id' => 6,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'push_subscriptions' => $subscriptionsCount
            ]);

            // Crear registro de mantenimiento (solo primer step)
            $mantenimiento = MantenimientoGeneral::create([
                'fechaSolicitud' => now()->format('Y-m-d'),
                'tarea' => 'TEST: Revisión de motor - ' . now()->format('H:i:s'),
                'solicitado' => 'Test Automático',
                'reparado' => 0, // No reparado (primer step solamente)
                'horas' => 0,
                'materiales' => '',
                'costo' => 0.0,
                'realizado' => '',
                'fechaRealizado' => null
            ]);

            Log::info('Registro de mantenimiento creado:', [
                'id' => $mantenimiento->id,
                'tarea' => $mantenimiento->tarea,
                'solicitado' => $mantenimiento->solicitado,
                'reparado' => $mantenimiento->reparado
            ]);

            // Esperar un momento para que el observer procese
            usleep(500000); // 0.5 segundos

            return response()->json([
                'success' => true,
                'message' => 'Test ejecutado correctamente',
                'mantenimiento' => [
                    'id' => $mantenimiento->id,
                    'tarea' => $mantenimiento->tarea,
                    'solicitado' => $mantenimiento->solicitado,
                    'fecha_solicitud' => $mantenimiento->fechaSolicitud,
                    'reparado' => $mantenimiento->reparado
                ],
                'user_info' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'push_subscriptions' => $subscriptionsCount
                ],
                'observer_info' => 'El observer debería haber enviado una notificación push al usuario ID 6. Revisa los logs para confirmar.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en test de mantenimiento:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error ejecutando el test: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function completeTestMantenimiento(Request $request)
    {
        try {
            $mantenimientoId = $request->input('id');
            
            if (!$mantenimientoId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de mantenimiento requerido'
                ], 400);
            }

            $mantenimiento = MantenimientoGeneral::find($mantenimientoId);
            
            if (!$mantenimiento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro de mantenimiento no encontrado'
                ], 404);
            }

            Log::info('=== COMPLETANDO MANTENIMIENTO TEST ===');
            
            // Actualizar para marcar como completado
            $mantenimiento->update([
                'reparado' => 1,
                'realizado' => 'Test Automático - Completado',
                'fechaRealizado' => now()->format('Y-m-d'),
                'horas' => 2,
                'materiales' => 'Aceite, filtros (TEST)',
                'costo' => 5000.00
            ]);

            Log::info('Mantenimiento marcado como completado:', [
                'id' => $mantenimiento->id,
                'reparado' => $mantenimiento->reparado,
                'realizado' => $mantenimiento->realizado,
                'fecha_realizado' => $mantenimiento->fechaRealizado
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento marcado como completado',
                'mantenimiento' => [
                    'id' => $mantenimiento->id,
                    'tarea' => $mantenimiento->tarea,
                    'reparado' => $mantenimiento->reparado,
                    'realizado' => $mantenimiento->realizado,
                    'fecha_realizado' => $mantenimiento->fechaRealizado,
                    'horas' => $mantenimiento->horas,
                    'costo' => $mantenimiento->costo
                ],
                'observer_info' => 'El observer debería haber enviado una notificación de completado al usuario ID 6.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error completando test de mantenimiento:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error completando el test: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRecentMantenimientos()
    {
        try {
            $mantenimientos = MantenimientoGeneral::where('tarea', 'LIKE', 'TEST:%')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['id', 'tarea', 'solicitado', 'reparado', 'fechaSolicitud', 'created_at']);

            return response()->json([
                'success' => true,
                'mantenimientos' => $mantenimientos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo registros: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cleanTestData()
    {
        try {
            $deleted = MantenimientoGeneral::where('tarea', 'LIKE', 'TEST:%')->delete();
            
            Log::info('Datos de test limpiados:', [
                'registros_eliminados' => $deleted
            ]);

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$deleted} registros de test"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error limpiando datos: ' . $e->getMessage()
            ], 500);
        }
    }
}
