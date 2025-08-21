<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PushSubscription;
use App\Models\User;

class PushSubscriptionsStatus extends Command
{
    protected $signature = 'push:status {user_id?}';
    protected $description = 'Mostrar el estado de las suscripciones push por usuario';

    public function handle()
    {
        $userId = $this->argument('user_id');

        if ($userId) {
            $this->showUserSubscriptions($userId);
        } else {
            $this->showAllSubscriptions();
        }
    }

    private function showUserSubscriptions($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario con ID {$userId} no encontrado");
            return;
        }

        $this->info("🔍 Suscripciones para usuario: {$user->name} (ID: {$userId})");
        $this->line("📧 Email: {$user->email}");

        $subscriptions = PushSubscription::where('user_id', $userId)->get();

        if ($subscriptions->isEmpty()) {
            $this->warn("❌ No hay suscripciones para este usuario");
            return;
        }

        $this->info("📱 Total suscripciones: " . $subscriptions->count());

        foreach ($subscriptions as $subscription) {
            $endpointType = str_contains($subscription->endpoint, 'fcm.googleapis.com') ? 'Chrome/FCM' : 'Other';
            $this->line("  - ID: {$subscription->id}, Tipo: {$endpointType}");
            $this->line("    Endpoint: " . substr($subscription->endpoint_hash, 0, 40) . "...");
            $this->line("    Creado: {$subscription->created_at}");
        }
    }

    private function showAllSubscriptions()
    {
        $this->info("🔍 Estado general de suscripciones push");

        $totalSubscriptions = PushSubscription::count();
        $totalUsers = User::count();
        $usersWithSubscriptions = PushSubscription::distinct('user_id')->count('user_id');

        $this->table(['Métrica', 'Valor'], [
            ['Total Suscripciones', $totalSubscriptions],
            ['Total Usuarios', $totalUsers],
            ['Usuarios con Suscripciones', $usersWithSubscriptions],
            ['% Adopción', $totalUsers > 0 ? round(($usersWithSubscriptions / $totalUsers) * 100, 2) . '%' : '0%'],
        ]);

        $this->line('');
        $this->info("👥 Suscripciones por usuario:");

        $subscriptionsByUser = PushSubscription::selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->get();

        foreach ($subscriptionsByUser as $item) {
            $user = User::find($item->user_id);
            $userName = $user ? $user->name : "Usuario #{$item->user_id}";
            $this->line("  - {$userName}: {$item->count} suscripción(es)");
        }
    }
}
