<?php

namespace App\Filament\Mantenimiento\Resources\RodadosHerramientasResource\Pages;

use App\Filament\Mantenimiento\Resources\RodadosHerramientasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRodadosHerramientas extends ListRecords
{
    protected static string $resource = RodadosHerramientasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Nuevo Rodado / Herramienta'), // Cambia este texto al deseado
        ];
    }

    public function getTitle(): string
    {
        return 'Rodados / Herramientas'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Rodados / Herramientas'; // Cambia este texto al breadcrumb deseado
    }
}
