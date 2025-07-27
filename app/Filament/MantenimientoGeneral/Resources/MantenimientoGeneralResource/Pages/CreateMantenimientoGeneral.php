<?php

namespace App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource\Pages;

use App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMantenimientoGeneral extends CreateRecord
{
    protected static string $resource = MantenimientoGeneralResource::class;

    public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Mantenimiento'; // Cambia este texto al breadcrumb deseado
    }

}
