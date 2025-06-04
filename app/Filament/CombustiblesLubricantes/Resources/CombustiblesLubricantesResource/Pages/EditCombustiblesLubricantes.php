<?php

namespace App\Filament\CombustiblesLubricantes\Resources\CombustiblesLubricantesResource\Pages;

use App\Filament\CombustiblesLubricantes\Resources\CombustiblesLubricantesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCombustiblesLubricantes extends EditRecord
{
    protected static string $resource = CombustiblesLubricantesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
