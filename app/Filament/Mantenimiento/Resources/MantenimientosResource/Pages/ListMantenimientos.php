<?php

namespace App\Filament\Mantenimiento\Resources\MantenimientosResource\Pages;

use App\Filament\Mantenimiento\Resources\MantenimientosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMantenimientos extends ListRecords
{
    protected static string $resource = MantenimientosResource::class;


        protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Cargar Manenimiento'), // Cambia este texto al deseado
        ];
    }

    public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Listado'; // Cambia este texto al breadcrumb deseado
    }
}
