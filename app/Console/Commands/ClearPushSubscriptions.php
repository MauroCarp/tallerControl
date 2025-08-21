<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PushSubscription;

class ClearPushSubscriptions extends Command
{
    protected $signature = 'push:clear';
    protected $description = 'Clear all push subscriptions from database';

    public function handle()
    {
        $count = PushSubscription::count();
        PushSubscription::truncate();
        
        $this->info("Eliminadas {$count} suscripciones de la base de datos.");
        $this->info("Base de datos limpia. Ahora puedes crear nuevas suscripciones.");
        
        return 0;
    }
}
