<?php

namespace App\Filament\Mantenimiento\Resources\RodadosHerramientasResource\Pages;

use App\Filament\Mantenimiento\Resources\RodadosHerramientasResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRodadosHerramientas extends CreateRecord
{
    protected static string $resource = RodadosHerramientasResource::class;

     public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Rodado/Herramienta'; // Cambia este texto al breadcrumb deseado
    }

}
