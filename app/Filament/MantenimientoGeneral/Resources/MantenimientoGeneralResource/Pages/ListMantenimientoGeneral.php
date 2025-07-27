<?php

namespace App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource\Pages;

use App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMantenimientoGeneral extends ListRecords
{
    protected static string $resource = MantenimientoGeneralResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Cargar Mantenimiento') // Cambia este texto al deseado
                ->visible(fn () => auth()->user()->hasRole('admin')), // Reemplaza 'nombre_del_grupo' por el nombre real del grupo
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
