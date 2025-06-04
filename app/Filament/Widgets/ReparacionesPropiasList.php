<?php

namespace App\Filament\Widgets;

use App\Models\Reparaciones;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;


class ReparacionesPropiasList extends BaseWidget
{
    protected static ?string $panel = 'roturasReparacion';

    protected static ?string $heading = 'Roturas/Reparaciones Propias';

    // Puedes cambiar esto si necesitas mostrar en el dashboard
    protected int|string|array $columnSpan = 'half';
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    // public function getIngresosProperty()
    // {
    //     return Mantenimientosservices::latest()->take(10)->get(); // Últimos 5 ingresos
    // }

   public function table(Table $table): Table
    {
        return $table
            ->query(
                Reparaciones::query()->select(['id','fecha', 'rodadoHerramienta_id', 'descripcion','encargado'])->where('tipo','Propio')->orderBy('fecha', 'desc')
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
                Tables\Columns\TextColumn::make('encargado')
                    ->label('Encargado'),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción de la Rotura'),
            ])->paginated(false)
            ->actions([
            Tables\Actions\Action::make('download_pdf')
                ->label('Reporte')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function ($record) {
                    $pdf = Pdf::loadView('pdf.mantenimiento', ['record' => $record]);
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'Reporte_Mantenimiento'.$record->fecha.'.pdf'
                    );
                }),
                Tables\Actions\ViewAction::make(),
            ]);
    }
}
