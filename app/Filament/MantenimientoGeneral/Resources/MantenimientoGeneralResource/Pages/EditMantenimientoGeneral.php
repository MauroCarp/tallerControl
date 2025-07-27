<?php

namespace App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource\Pages;

use App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMantenimientoGeneral extends EditRecord
{
    protected static string $resource = MantenimientoGeneralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
