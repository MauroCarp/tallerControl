<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MantenimientoGeneral extends Model
{
    use HasFactory;

    protected $guarded = [];



    // Método para reorganizar prioridades
    public static function reorganizarPrioridades($nuevaPrioridad, $excludeId = null)
    {
        // Obtener tareas activas (reparado = 0) ordenadas por prioridad
        $tareasActivas = static::where('reparado', 0)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where('prioridad_orden', '>=', $nuevaPrioridad)
            ->orderBy('prioridad_orden')
            ->get();

        // Reorganizar prioridades incrementando en 1
        foreach ($tareasActivas as $tarea) {
            $tarea->update(['prioridad_orden' => $tarea->prioridad_orden + 1]);
        }
    }
}
