<?php

namespace App\Http\Controllers;

use App\Models\MantenimientoGeneral;
use App\Models\User;
use App\Models\PushSubscription;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TestMantenimientoController extends Controller
{
    private PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Mostrar la página de testing
     */
    public function index()
    {
        // Obtener información del estado actual
        $usuario6 = User::find(6);
        $suscripciones = PushSubscription::where('user_id', 6)->count();
        $mantenimientos = MantenimientoGeneral::count();
        
        return view('test-mantenimiento', [
            'usuario6' => $usuario6,
            'suscripciones' => $suscripciones,
            'mantenimientos' => $mantenimientos
        ]);
    }

    /**
     * Crear un registro de mantenimiento general de prueba
     */
    public function crearMantenimientoPrueba(Request $request): JsonResponse
    {
        try {
            // Validar que el usuario ID 6 existe
            $usuario6 = User::find(6);
            if (!$usuario6) {
                return response()->json([
                    'success' => false,
                    'error' => 'El usuario con ID 6 no existe. Créalo primero.',
                    'details' => 'Se necesita un usuario con ID 6 para recibir las notificaciones.'
                ], 400);
            }

            // Verificar si hay suscripciones push para el usuario 6
            $suscripciones = PushSubscription::where('user_id', 6)->count();
            
            // Datos del mantenimiento de prueba
            $tipoMantenimiento = $request->input('tipo', 'completo');
            
            $datosMantenimiento = $this->obtenerDatosMantenimiento($tipoMantenimiento);

            // Crear el registro de mantenimiento (esto disparará automáticamente el observer)
            $mantenimiento = MantenimientoGeneral::create($datosMantenimiento);

            // Obtener información adicional para la respuesta
            $response = [
                'success' => true,
                'message' => 'Mantenimiento creado exitosamente',
                'data' => [
                    'mantenimiento_id' => $mantenimiento->id,
                    'tarea' => $mantenimiento->tarea,
                    'solicitado' => $mantenimiento->solicitado,
                    'usuario_destino' => [
                        'id' => 6,
                        'nombre' => $usuario6->name,
                        'email' => $usuario6->email
                    ],
                    'suscripciones_push' => $suscripciones,
                    'observer_ejecutado' => true,
                    'timestamp' => now()->toISOString()
                ]
            ];

            // Si no hay suscripciones, agregar una advertencia
            if ($suscripciones === 0) {
                $response['warning'] = 'El usuario ID 6 no tiene suscripciones push activas. El observer se ejecutó pero no se enviaron notificaciones.';
            }

            Log::info('Mantenimiento de prueba creado desde interfaz web', [
                'mantenimiento_id' => $mantenimiento->id,
                'tipo' => $tipoMantenimiento,
                'user_id_destino' => 6,
                'suscripciones_disponibles' => $suscripciones
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error creando mantenimiento de prueba', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear usuario de prueba con ID 6
     */
    public function crearUsuario6(): JsonResponse
    {
        try {
            // Verificar si ya existe
            $usuarioExistente = User::find(6);
            if ($usuarioExistente) {
                return response()->json([
                    'success' => false,
                    'error' => 'El usuario con ID 6 ya existe',
                    'data' => [
                        'id' => $usuarioExistente->id,
                        'name' => $usuarioExistente->name,
                        'email' => $usuarioExistente->email
                    ]
                ]);
            }

            // Crear el usuario con ID específico
            $usuario = new User();
            $usuario->id = 6;
            $usuario->name = 'Carlos Morelli';
            $usuario->email = 'carlos.morelli@tallercontrol.com';
            $usuario->password = bcrypt('password123');
            $usuario->email_verified_at = now();
            $usuario->save();

            return response()->json([
                'success' => true,
                'message' => 'Usuario Carlos Morelli (ID 6) creado exitosamente',
                'data' => [
                    'id' => $usuario->id,
                    'name' => $usuario->name,
                    'email' => $usuario->email
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error creando usuario',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear suscripción push de prueba para el usuario 6
     */
    public function crearSuscripcionPrueba(): JsonResponse
    {
        try {
            // Verificar que el usuario 6 existe
            $usuario6 = User::find(6);
            if (!$usuario6) {
                return response()->json([
                    'success' => false,
                    'error' => 'Primero debes crear el usuario con ID 6'
                ], 400);
            }

            // Crear suscripción de prueba
            $suscripcion = PushSubscription::create([
                'user_id' => 6,
                'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint-' . uniqid(),
                'public_key' => 'test-public-key-' . uniqid(),
                'auth_token' => 'test-auth-token-' . uniqid(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Suscripción push de prueba creada para Carlos Morelli',
                'data' => [
                    'subscription_id' => $suscripcion->id,
                    'user_id' => $suscripcion->user_id,
                    'endpoint' => substr($suscripcion->endpoint, 0, 50) . '...'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error creando suscripción',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el estado actual del sistema
     */
    public function obtenerEstado(): JsonResponse
    {
        $usuario6 = User::find(6);
        $suscripciones = PushSubscription::where('user_id', 6)->get();
        $mantenimientos = MantenimientoGeneral::orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'usuario_6' => $usuario6 ? [
                'existe' => true,
                'id' => $usuario6->id,
                'name' => $usuario6->name,
                'email' => $usuario6->email
            ] : ['existe' => false],
            'suscripciones_push' => [
                'count' => $suscripciones->count(),
                'list' => $suscripciones->map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'endpoint' => substr($sub->endpoint, 0, 50) . '...',
                        'created_at' => $sub->created_at
                    ];
                })
            ],
            'mantenimientos_recientes' => $mantenimientos->map(function ($mant) {
                return [
                    'id' => $mant->id,
                    'tarea' => $mant->tarea,
                    'created_at' => $mant->created_at,
                    'reparado' => $mant->reparado
                ];
            })
        ]);
    }

    /**
     * Limpiar datos de prueba
     */
    public function limpiarDatosPrueba(): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Eliminar suscripciones del usuario 6
            $suscripcionesEliminadas = PushSubscription::where('user_id', 6)->delete();

            // Eliminar mantenimientos de prueba (últimos 10)
            $mantenimientosEliminados = MantenimientoGeneral::orderBy('created_at', 'desc')
                ->take(10)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Datos de prueba limpiados',
                'data' => [
                    'suscripciones_eliminadas' => $suscripcionesEliminadas,
                    'mantenimientos_eliminados' => $mantenimientosEliminados
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Error limpiando datos',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener diferentes tipos de datos para mantenimiento (solo primer paso - SOLICITUD)
     */
    private function obtenerDatosMantenimiento(string $tipo): array
    {
        $base = [
            'fechaSolicitud' => now()->format('Y-m-d'),
            'solicitado' => 'Mantenimiento', // Simular que viene del sector Mantenimiento
        ];

        switch ($tipo) {
            case 'frenos':
                return array_merge($base, [
                    'tarea' => 'Revisión sistema de frenos',
                    'prioridad' => 'ALTA',
                ]);
            
            case 'motor':
                return array_merge($base, [
                    'tarea' => 'Mantenimiento preventivo motor',
                    'prioridad' => 'NORMAL',
                ]);
            
            case 'electrico':
                return array_merge($base, [
                    'tarea' => 'Diagnóstico eléctrico',
                    'prioridad' => 'MUY ALTA',
                ]);
            
            default: // 'completo'
                return array_merge($base, [
                    'tarea' => 'Mantenimiento integral completo',
                    'prioridad' => 'ALTA',
                ]);
        }
    }
}
