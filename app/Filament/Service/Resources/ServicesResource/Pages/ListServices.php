<?php

namespace App\Filament\Service\Resources\ServicesResource\Pages;

use App\Filament\Service\Resources\ServicesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    protected static string $resource = ServicesResource::class;

    
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Cargar Service'), // Cambia este texto al deseado
        ];
    }

    public function getTitle(): string
    {
        return 'Services'; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Services'; // Cambia este texto al breadcrumb deseado
    }
}
