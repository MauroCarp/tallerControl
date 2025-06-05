<?php

namespace App\Filament\RoturasReparacion\Resources\RoturasReparacionResource\Pages;

use App\Filament\RoturasReparacion\Resources\RoturasReparacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoturasReparacions extends ListRecords
{
    protected static string $resource = RoturasReparacionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }


        public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Listado'; // Cambia este texto al título deseado
    }
}
