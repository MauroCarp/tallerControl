<?php

namespace App\Filament\Widgets;

use App\Models\Mantenimientosservices;
use App\Models\Rodados;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;

class CronogramaMantenimientos extends BaseWidget
{

     // Opcional: Solo mostrar en panel 'mantenimiento'
    protected static ?string $panel = 'mantenimiento';

    protected static ?string $heading = 'Cronograma Mantenimiento';
    // Puedes cambiar esto si necesitas mostrar en el dashboard
    protected int|string|array $columnSpan = 'half';
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;
    

    public function table(Table $table): Table
        {
            return $table
                ->query(
                    Mantenimientosservices::query()->select(['fecha', 'rodadoHerramienta_id', 'responsable'])
                )
                ->columns([
                    Tables\Columns\TextColumn::make('nombre')
                        ->label('Rodados/Herramientas'),
                    Tables\Columns\TextColumn::make('frecuencia')
                        ->label('Frecuencia'),
                    Tables\Columns\TextColumn::make('agenda')
                        ->label(''),
                ])->paginated(false); 
        }


}
