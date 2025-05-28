<?php

namespace App\Filament\Widgets;

use App\Models\RodadosHerramientas;
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
                    RodadosHerramientas::query()->select(['id','nombre', 'frecuencia', 'agenda'])
                    ->orderBy('frecuencia', 'desc')
                )
                ->columns([
                    Tables\Columns\TextColumn::make('nombre')
                        ->label('Rodados/Herramientas'),
                    Tables\Columns\TextColumn::make('frecuencia')
                        ->label('Frecuencia')
                        ->formatStateUsing(function ($state){
                            if($state == 0) {

                            return 'Cada vez que se use';
                            
                            } else {

                                return $state . ' días';

                            }
                        }),
                    Tables\Columns\TextColumn::make('agenda')
                        ->label('')
                        ->formatStateUsing(function ($state){
                            $agenda = json_decode(str_replace("'",'"',$state), true);

                            if (is_null($agenda)) {
                                return '';
                            }


                            $diaKey = array_key_first($agenda);
                            $turno = $agenda[$diaKey] ?? '';
                            return $diaKey . ' de la ' . $turno;
                            
                        }),

                ])->paginated(false); 
        }


}
