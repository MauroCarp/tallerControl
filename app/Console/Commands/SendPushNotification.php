<?php

namespace App\Console\Commands;

use App\Services\PushNotificationService;
use Illuminate\Console\Command;

class SendPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:send 
                            {title : El título de la notificación}
                            {message : El mensaje de la notificación}
                            {--user= : ID del usuario específico (opcional)}
                            {--icon= : URL del icono (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar una push notification a usuarios suscritos';

    private PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        parent::__construct();
        $this->pushService = $pushService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $title = $this->argument('title');
        $message = $this->argument('message');
        $userId = $this->option('user');
        $icon = $this->option('icon') ?? '/images/icons/icon-192x192.png';

        $payload = [
            'title' => $title,
            'body' => $message,
            'icon' => $icon,
            'badge' => '/images/icons/icon-72x72.png',
            'tag' => 'console-notification',
            'data' => [
                'url' => url('/'),
                'timestamp' => now()->toISOString(),
                'source' => 'console'
            ]
        ];

        try {
            if ($userId) {
                $this->info("Enviando notificación al usuario ID: {$userId}");
                $successCount = $this->pushService->sendToUser($userId, $payload);
            } else {
                $this->info("Enviando notificación a todos los usuarios suscritos");
                $successCount = $this->pushService->sendToAll($payload);
            }

            $this->info("✅ Notificación enviada exitosamente a {$successCount} dispositivo(s)");
            
            if ($successCount === 0) {
                $this->warn("⚠️  No se encontraron suscripciones activas");
            }

        } catch (\Exception $e) {
            $this->error("❌ Error al enviar la notificación: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
