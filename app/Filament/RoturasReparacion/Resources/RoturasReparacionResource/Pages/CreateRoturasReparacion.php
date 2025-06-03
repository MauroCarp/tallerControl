<?php

namespace App\Filament\RoturasReparacion\Resources\RoturasReparacionResource\Pages;

use App\Filament\RoturasReparacion\Resources\RoturasReparacionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRoturasReparacion extends CreateRecord
{
    protected static string $resource = RoturasReparacionResource::class;

    public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Registro'; // Cambia este texto al breadcrumb deseado
    }

}
