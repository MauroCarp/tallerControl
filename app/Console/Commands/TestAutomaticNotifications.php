<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MantenimientoGeneral;
use App\Models\User;
use App\Models\PushSubscription;

class TestAutomaticNotifications extends Command
{
    protected $signature = 'push:test-automatic';
    protected $description = 'Test automatic push notifications for MantenimientoGeneral';

    public function handle()
    {
        $this->info('Testing automatic push notifications...');
        $this->line('');

        // Verificar usuario 6
        $user = User::find(6);
        if (!$user) {
            $this->error('❌ Usuario ID 6 no existe');
            return 1;
        }
        $this->info("✅ Usuario ID 6 encontrado: {$user->name}");

        // Verificar suscripciones del usuario 6
        $subscriptions = PushSubscription::where('user_id', 6)->count();
        $this->line("📱 Suscripciones del usuario 6: {$subscriptions}");
        
        if ($subscriptions == 0) {
            $this->warn('⚠️  El usuario 6 no tiene suscripciones push activas');
            $this->line('   Para que reciba notificaciones, debe:');
            $this->line('   1. Ir a http://127.0.0.1:8000/push-test');
            $this->line('   2. Seguir los pasos para suscribirse');
            $this->line('   3. Asegurarse de estar logueado como usuario ID 6');
        }

        $this->line('');
        $this->info('Creando un registro de MantenimientoGeneral de prueba...');

        // Crear un registro de prueba
        $mantenimiento = MantenimientoGeneral::create([
            'tarea' => 'Prueba de notificación automática - ' . now()->format('Y-m-d H:i:s'),
            'fechaSolicitud' => now(),
            'fechaRealizar' => now()->addDays(1),
            'solicitado' => 'Sistema Automático',
            'prioridad' => 'Alta',
            'reparado' => 0,
        ]);

        $this->info("✅ Registro creado con ID: {$mantenimiento->id}");
        $this->line('   Si el usuario 6 tiene suscripciones, debería recibir una notificación');
        
        $this->line('');
        $this->info('Marcando el registro como completado...');
        
        // Actualizar a completado
        $mantenimiento->update(['reparado' => 1]);
        
        $this->info("✅ Registro marcado como completado");
        $this->line('   Si el usuario 6 tiene suscripciones, debería recibir otra notificación');

        $this->line('');
        $this->info('Revisa los logs para ver el resultado:');
        $this->line('   tail -f storage/logs/laravel.log');

        return 0;
    }
}
