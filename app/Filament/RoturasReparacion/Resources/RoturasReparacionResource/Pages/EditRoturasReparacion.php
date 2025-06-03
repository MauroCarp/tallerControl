<?php

namespace App\Filament\RoturasReparacion\Resources\RoturasReparacionResource\Pages;

use App\Filament\RoturasReparacion\Resources\RoturasReparacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoturasReparacion extends EditRecord
{
    protected static string $resource = RoturasReparacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
