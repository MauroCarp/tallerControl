<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ValidateVapidKey extends Command
{
    protected $signature = 'push:validate-vapid';
    protected $description = 'Validar la clave VAPID para notificaciones push';

    public function handle()
    {
        $this->info('🔍 Validando clave VAPID...');
        $this->newLine();

        $publicKey = config('app.vapid_public_key');
        $privateKey = config('app.vapid_private_key');

        if (!$publicKey || !$privateKey) {
            $this->error('❌ Claves VAPID no configuradas en .env');
            return 1;
        }

        $this->info('📋 Información de las claves VAPID:');
        $this->line("   Clave pública: {$publicKey}");
        $this->line("   Longitud: " . strlen($publicKey) . " caracteres");
        $this->line("   Primera parte: " . substr($publicKey, 0, 20) . '...');
        $this->line("   Última parte: ..." . substr($publicKey, -20));
        $this->newLine();

        // Verificar formato base64url
        $isValidBase64Url = preg_match('/^[A-Za-z0-9_-]+$/', $publicKey);
        $this->line("   Formato base64url: " . ($isValidBase64Url ? '✅ Válido' : '❌ Inválido'));

        // Verificar longitud esperada (88 caracteres para VAPID)
        $expectedLength = 88;
        $lengthValid = strlen($publicKey) == $expectedLength;
        $this->line("   Longitud esperada ({$expectedLength}): " . ($lengthValid ? '✅ Correcta' : '❌ Incorrecta'));

        $this->newLine();

        // Intentar decodificar
        try {
            $decoded = $this->base64UrlDecode($publicKey);
            $this->info("✅ Clave decodificada correctamente");
            $this->line("   Bytes decodificados: " . strlen($decoded));
            
            if (strlen($decoded) === 65) {
                $this->info("✅ Longitud de clave decodificada correcta (65 bytes)");
            } else {
                $this->warn("⚠️ Longitud de clave decodificada inesperada: " . strlen($decoded) . " bytes");
            }
        } catch (\Exception $e) {
            $this->error("❌ Error decodificando clave: " . $e->getMessage());
            return 1;
        }

        $this->newLine();
        
        if ($isValidBase64Url && $lengthValid) {
            $this->info('🎉 La clave VAPID parece ser válida');
            $this->info('💡 Intenta acceder a /chrome-debug para hacer pruebas');
        } else {
            $this->error('❌ La clave VAPID tiene problemas');
            $this->warn('💡 Genera nuevas claves con: php artisan push:generate-vapid');
        }

        return 0;
    }

    private function base64UrlDecode($data)
    {
        $padded = $data . str_repeat('=', (4 - strlen($data) % 4) % 4);
        return base64_decode(strtr($padded, '-_', '+/'));
    }
}
