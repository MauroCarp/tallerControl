<?php

namespace App\Filament\Mantenimiento\Resources\MantenimientosResource\Pages;

use App\Filament\Mantenimiento\Resources\MantenimientosResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMantenimientos extends CreateRecord
{
    protected static string $resource = MantenimientosResource::class;

    public function getTitle(): string
    {
        return 'Nuevo Mantenimiento'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Mantenimiento'; // Cambia este texto al breadcrumb deseado
    }
}
