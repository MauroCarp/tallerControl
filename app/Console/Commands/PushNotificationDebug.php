<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PushNotificationDebug extends Command
{
    protected $signature = 'push:debug';
    protected $description = 'Verificar configuración de notificaciones push';

    public function handle()
    {
        $this->info('🔔 Verificando configuración de notificaciones push...');
        $this->newLine();

        // Verificar claves VAPID
        $publicKey = config('app.vapid_public_key');
        $privateKey = config('app.vapid_private_key');
        $subject = config('app.vapid_subject');

        $this->info('✅ Claves VAPID:');
        $this->line("   Public Key: " . ($publicKey ? substr($publicKey, 0, 20) . '...' : '❌ NO CONFIGURADA'));
        $this->line("   Private Key: " . ($privateKey ? '✅ Configurada' : '❌ NO CONFIGURADA'));
        $this->line("   Subject: " . ($subject ?: '❌ NO CONFIGURADO'));
        $this->newLine();

        // Verificar archivos JavaScript
        $jsFiles = [
            'public/js/push-notifications.js',
            'public/serviceworker.js'
        ];

        $this->info('📁 Archivos JavaScript:');
        foreach ($jsFiles as $file) {
            $exists = file_exists(base_path($file));
            $this->line("   {$file}: " . ($exists ? '✅ Existe' : '❌ No encontrado'));
        }
        $this->newLine();

        // Verificar modelo PushSubscription
        $modelExists = class_exists('App\Models\PushSubscription');
        $this->info('📊 Modelo PushSubscription: ' . ($modelExists ? '✅ Existe' : '❌ No encontrado'));
        
        if ($modelExists) {
            try {
                $count = \App\Models\PushSubscription::count();
                $this->line("   Total suscripciones: {$count}");
            } catch (\Exception $e) {
                $this->line("   ⚠️ Error contando suscripciones: " . $e->getMessage());
            }
        }
        $this->newLine();

        // Verificar configuración PWA
        $pwaConfig = config('laravelpwa');
        $this->info('🌐 Configuración PWA:');
        $this->line("   Nombre: " . ($pwaConfig['manifest']['name'] ?? '❌ No configurado'));
        $this->line("   GCM Sender ID: " . ($pwaConfig['manifest']['custom']['gcm_sender_id'] ?? '❌ No configurado'));
        $this->newLine();

        // Verificar permisos de archivos
        $this->info('🔒 Permisos de archivos:');
        $storageWritable = is_writable(storage_path());
        $this->line("   Storage writable: " . ($storageWritable ? '✅ Sí' : '❌ No'));
        
        $publicWritable = is_writable(public_path());
        $this->line("   Public writable: " . ($publicWritable ? '✅ Sí' : '❌ No'));
        $this->newLine();

        if ($publicKey && $privateKey && $subject) {
            $this->info('🎉 Configuración básica completada. Prueba las notificaciones en /push-test');
        } else {
            $this->error('❌ Faltan configuraciones importantes. Revisa las claves VAPID en el archivo .env');
        }

        return 0;
    }
}
