<?php

namespace App\Observers;

use App\Models\MantenimientoGeneral;

class MantenimientoGeneralObserver
{
    /**
     * Handle the MantenimientoGeneral "created" event.
     */
    public function created(MantenimientoGeneral $mantenimientoGeneral): void
    {
        //
    }

    /**
     * Handle the MantenimientoGeneral "updated" event.
     */
    public function updated(MantenimientoGeneral $mantenimientoGeneral): void
    {
        //
    }

    /**
     * Handle the MantenimientoGeneral "deleted" event.
     */
    public function deleted(MantenimientoGeneral $mantenimientoGeneral): void
    {
        //
    }

    /**
     * Handle the MantenimientoGeneral "restored" event.
     */
    public function restored(MantenimientoGeneral $mantenimientoGeneral): void
    {
        //
    }

    /**
     * Handle the MantenimientoGeneral "force deleted" event.
     */
    public function forceDeleted(MantenimientoGeneral $mantenimientoGeneral): void
    {
        //
    }

    public function creating(MantenimientoGeneral $mantenimiento)
    {
        if ($mantenimiento->prioridad_orden && $mantenimiento->reparado == 0) {
            MantenimientoGeneral::reorganizarPrioridades($mantenimiento->prioridad_orden);
        }
    }

    public function updating(MantenimientoGeneral $mantenimiento)
    {
        if ($mantenimiento->isDirty('prioridad_orden') && $mantenimiento->reparado == 0) {
            MantenimientoGeneral::reorganizarPrioridades(
                $mantenimiento->prioridad_orden, 
                $mantenimiento->id
            );
        }
    }
}
