<?php

namespace App\Filament\Mantenimiento\Resources\RodadosHerramientasResource\Pages;

use App\Filament\Mantenimiento\Resources\RodadosHerramientasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRodadosHerramientas extends EditRecord
{
    protected static string $resource = RodadosHerramientasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
