<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\WebPush;

class TestVapidKeys extends Command
{
    protected $signature = 'push:test-vapid';
    protected $description = 'Test VAPID keys configuration';

    public function handle()
    {
        $publicKey = config('app.vapid_public_key');
        $privateKey = config('app.vapid_private_key');
        $subject = config('app.vapid_subject');

        $this->info('Testing VAPID configuration...');
        $this->line('');
        
        $this->line("Public Key: {$publicKey}");
        $this->line("Public Key Length: " . strlen($publicKey));
        $this->line("Private Key: {$privateKey}");
        $this->line("Private Key Length: " . strlen($privateKey));
        $this->line("Subject: {$subject}");
        $this->line('');

        try {
            // Intentar crear una instancia de WebPush
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => $subject,
                    'publicKey' => $publicKey,
                    'privateKey' => $privateKey,
                ],
            ]);
            
            $this->info('✅ WebPush instance created successfully');
            $this->info('✅ VAPID keys are valid');
            
        } catch (\Exception $e) {
            $this->error('❌ Error creating WebPush instance:');
            $this->error($e->getMessage());
        }

        return 0;
    }
}
