<?php

namespace App\Filament\Service\Resources\ServicesResource\Pages;

use App\Filament\Service\Resources\ServicesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServices extends CreateRecord
{
    protected static string $resource = ServicesResource::class;

    public function getTitle(): string
    {
        return ' '; // Cambia este texto al título deseado

    }

    public function getBreadcrumb(): string
    {
        return 'Nuevo Service'; // Cambia este texto al breadcrumb deseado
    }
}
