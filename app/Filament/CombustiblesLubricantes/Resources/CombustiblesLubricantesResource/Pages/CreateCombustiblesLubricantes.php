<?php

namespace App\Filament\CombustiblesLubricantes\Resources\CombustiblesLubricantesResource\Pages;

use App\Filament\CombustiblesLubricantes\Resources\CombustiblesLubricantesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCombustiblesLubricantes extends CreateRecord
{
    protected static string $resource = CombustiblesLubricantesResource::class;

     public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Registro'; // Cambia este texto al breadcrumb deseado
    }
}
