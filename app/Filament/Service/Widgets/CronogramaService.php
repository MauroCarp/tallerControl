<?php

namespace App\Filament\Service\Widgets;

use App\Models\RodadosHerramientas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CronogramaService extends BaseWidget
{

    protected static ?string $panel = 'service';

    protected static ?string $heading = 'Cronograma Services';
    // Puedes cambiar esto si necesitas mostrar en el dashboard
    protected int|string|array $columnSpan = 'half';
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {

        
        return $table
            ->query(
                    RodadosHerramientas::query()->select(['id','nombre', 'serviceHoras', 'unidadService'])
                    ->orderBy('frecuencia', 'desc')
                )
                ->columns([
                    Tables\Columns\TextColumn::make('nombre')
                        ->label('Rodados/Herramientas')
                        ->extraAttributes(['style' => 'height: 40px;line-height: 2em;']),
                    Tables\Columns\TextColumn::make('serviceHoras')
                        ->label('Frecuencia de Service')
                        ->extraAttributes(['style' => 'height: 40px;line-height: 2em;'])
                        ->formatStateUsing(function ($state,$record){
                            if($state == 0) {

                            return '-';
                            
                            } else {

                                return $state . ' ' . $record->unidadService;

                            }
                        }),

                ])->paginated(false);
    }
}
