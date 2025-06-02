<?php

namespace App\Filament\Widgets;

use App\Models\Mantenimientosservices;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ServiceList extends BaseWidget
{
    protected static ?string $panel = 'service';

    protected static ?string $heading = 'Planilla Services';

    // Puedes cambiar esto si necesitas mostrar en el dashboard
    protected int|string|array $columnSpan = 'half';
    protected static ?int $sort = 2;
    protected static bool $isLazy = false;

    // public function getIngresosProperty()
    // {
    //     return Mantenimientosservices::latest()->take(10)->get(); // Últimos 5 ingresos
    // }

   public function table(Table $table): Table
    {
        return $table
            ->query(
                Mantenimientosservices::query()->select(['id','fecha', 'rodadoHerramienta_id', 'responsable'])->where('isMantenimiento','0')->orderBy('fecha', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('rodadoHerramienta_id')
                    ->label('Rodado/Herramienta')
                    ->formatStateUsing(function ($state) {
                        return \App\Models\RodadosHerramientas::find($state)?->nombre ?? '';
                    }),
                Tables\Columns\TextColumn::make('responsable')
                    ->label('Responsables'),
            ])->paginated(false);
    }
}
