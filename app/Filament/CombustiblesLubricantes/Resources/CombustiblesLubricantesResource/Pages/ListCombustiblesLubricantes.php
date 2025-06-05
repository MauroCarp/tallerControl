<?php

namespace App\Filament\CombustiblesLubricantes\Resources\CombustiblesLubricantesResource\Pages;

use App\Filament\CombustiblesLubricantes\Resources\CombustiblesLubricantesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCombustiblesLubricantes extends ListRecords
{
    protected static string $resource = CombustiblesLubricantesResource::class;

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
        return 'Listado'; // Cambia este texto al breadcrumb deseado
    }
    
}
