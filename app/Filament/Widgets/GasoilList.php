<?php

namespace App\Filament\Widgets;

use App\Models\Combustibles;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class GasoilList extends BaseWidget
{
    protected static ?string $panel = 'combustiblesLubricantes';

    protected static ?string $heading = 'GASOIL';

    // Puedes cambiar esto si necesitas mostrar en el dashboard
    // protected int|string|array $columnSpan = 'half';
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    public function getIngresosProperty()
    {
        return Mantenimientosservices::latest()->take(10)->where('tipo','Gasoil')->get(); // Últimos 5 ingresos
    }

   public function table(Table $table): Table
    {
        return $table
            ->query(
                Combustibles::query()->select(['id','fecha', 'ingresoLitros', 'origen','egresoLitros','destino'])->where('tipo','Gasoil')->orderBy('fecha', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y'),
            Tables\Columns\TextColumn::make('ingresoLitros')
                ->label('Ingreso (Lts)'),
            Tables\Columns\TextColumn::make('origen')
                ->label('Origen'),
            Tables\Columns\TextColumn::make('egresoLitros')
                ->label('Egreso (Lts)'),
            Tables\Columns\TextColumn::make('destino')
                ->label('Destino')
                ->formatStateUsing(function ($state) {
                    // Buscar el nombre en el modelo HerramientasRodados según el id (destino)
                    return \App\Models\RodadosHerramientas::find($state)?->nombre ?? '-';
                }),
            ])->paginated(false);
    }
}
