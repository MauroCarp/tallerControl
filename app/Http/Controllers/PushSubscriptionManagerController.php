<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PushSubscriptionManagerController extends Controller
{
    /**
     * Mostrar la página de gestión de suscripciones
     */
    public function index()
    {
        $users = User::with(['pushSubscriptions'])
            ->whereHas('pushSubscriptions')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'subscriptions_count' => $user->pushSubscriptions->count(),
                    'latest_subscription' => $user->pushSubscriptions->sortByDesc('created_at')->first()?->created_at?->format('d/m/Y H:i'),
                ];
            });

        $allUsers = User::orderBy('name')->get();
        $totalSubscriptions = PushSubscription::count();
        $usersWithSubscriptions = User::whereHas('pushSubscriptions')->count();

        return view('push-subscription-manager', compact('users', 'allUsers', 'totalSubscriptions', 'usersWithSubscriptions'));
    }

    /**
     * Obtener suscripciones de un usuario específico
     */
    public function getUserSubscriptions(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'ID de usuario requerido'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $subscriptions = PushSubscription::where('user_id', $userId)
            ->get()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'endpoint_preview' => substr($subscription->endpoint, 0, 60) . '...',
                    'endpoint_hash' => $subscription->endpoint_hash,
                    'created_at' => $subscription->created_at->format('d/m/Y H:i:s'),
                    'updated_at' => $subscription->updated_at->format('d/m/Y H:i:s'),
                ];
            });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'subscriptions' => $subscriptions,
            'total' => $subscriptions->count()
        ]);
    }

    /**
     * Eliminar todas las suscripciones de un usuario
     */
    public function deleteUserSubscriptions(Request $request): JsonResponse
    {
        $userId = $request->get('user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'ID de usuario requerido'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $deletedCount = PushSubscription::where('user_id', $userId)->delete();

        \Log::info('Suscripciones eliminadas manualmente', [
            'user_id' => $userId,
            'user_email' => $user->email,
            'deleted_count' => $deletedCount,
            'admin_user' => auth()->user()?->email ?? 'unknown'
        ]);

        return response()->json([
            'message' => "Se eliminaron {$deletedCount} suscripciones del usuario {$user->name}",
            'deleted_count' => $deletedCount,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    /**
     * Eliminar una suscripción específica
     */
    public function deleteSubscription(Request $request): JsonResponse
    {
        $subscriptionId = $request->get('subscription_id');
        
        if (!$subscriptionId) {
            return response()->json(['error' => 'ID de suscripción requerido'], 400);
        }

        $subscription = PushSubscription::find($subscriptionId);
        if (!$subscription) {
            return response()->json(['error' => 'Suscripción no encontrada'], 404);
        }

        $user = $subscription->user;
        $subscription->delete();

        \Log::info('Suscripción individual eliminada', [
            'subscription_id' => $subscriptionId,
            'user_id' => $subscription->user_id,
            'user_email' => $user?->email ?? 'unknown',
            'endpoint_hash' => $subscription->endpoint_hash,
            'admin_user' => auth()->user()?->email ?? 'unknown'
        ]);

        return response()->json([
            'message' => 'Suscripción eliminada correctamente',
            'subscription_id' => $subscriptionId,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ] : null
        ]);
    }

    /**
     * Obtener estadísticas generales
     */
    public function getStats(): JsonResponse
    {
        $totalSubscriptions = PushSubscription::count();
        $usersWithSubscriptions = User::whereHas('pushSubscriptions')->count();
        $totalUsers = User::count();
        $subscriptionsLast30Days = PushSubscription::where('created_at', '>=', now()->subDays(30))->count();

        $topUsers = User::withCount('pushSubscriptions')
            ->having('push_subscriptions_count', '>', 0)
            ->orderBy('push_subscriptions_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'subscriptions_count' => $user->push_subscriptions_count
                ];
            });

        return response()->json([
            'total_subscriptions' => $totalSubscriptions,
            'users_with_subscriptions' => $usersWithSubscriptions,
            'total_users' => $totalUsers,
            'subscriptions_last_30_days' => $subscriptionsLast30Days,
            'top_users' => $topUsers
        ]);
    }

    /**
     * Limpiar todas las suscripciones (solo para admin)
     */
    public function cleanAllSubscriptions(Request $request): JsonResponse
    {
        // Verificar que el usuario tenga permisos de admin
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'No tienes permisos para esta acción'], 403);
        }

        $totalDeleted = PushSubscription::count();
        PushSubscription::truncate();

        \Log::warning('TODAS las suscripciones fueron eliminadas', [
            'deleted_count' => $totalDeleted,
            'admin_user' => auth()->user()->email,
            'timestamp' => now()
        ]);

        return response()->json([
            'message' => "Se eliminaron TODAS las {$totalDeleted} suscripciones de la base de datos",
            'deleted_count' => $totalDeleted,
            'warning' => 'Esta acción afectó a todos los usuarios'
        ]);
    }
}
