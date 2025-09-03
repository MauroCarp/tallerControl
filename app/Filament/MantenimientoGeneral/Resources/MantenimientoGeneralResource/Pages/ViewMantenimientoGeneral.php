<?php

namespace App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource\Pages;

use App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMantenimientoGeneral extends ViewRecord
{
    protected static string $resource = MantenimientoGeneralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
