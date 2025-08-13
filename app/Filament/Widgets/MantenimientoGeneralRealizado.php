<?php

namespace App\Filament\Widgets;

use App\Models\MantenimientoGeneral;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MantenimientoGeneralRealizado extends BaseWidget
{
    // protected static string $view = 'filament.widgets.mantenimientos-list';

    // Opcional: Solo mostrar en panel 'mantenimiento'
    protected static ?string $panel = 'mantenimientoGeneral';

    protected static ?string $heading = 'Mantenimientos Realizados';
    // Puedes cambiar esto si necesitas mostrar en el dashboard
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;

    public function getIngresosProperty()
    {
        return MantenimientoGeneral::latest()->take(10)->get(); // Últimos 5 ingresos
    }

   public function table(Table $table): Table
    {
        return $table
            ->query(
                MantenimientoGeneral::query()->select(['id','tarea','realizado','horas','materiales','costo','fechaRealizado'])->where('reparado','1')->orderBy('fechaRealizado', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tarea')
                    ->label('Tarea'),
                Tables\Columns\TextColumn::make('realizado')
                    ->label('Realizado por'),
                Tables\Columns\TextColumn::make('horas')
                    ->label('Horas'),
                Tables\Columns\TextColumn::make('materiales')
                    ->label('Materiales'),
                Tables\Columns\TextColumn::make('costo')
                    ->label('Costo')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),
                Tables\Columns\TextColumn::make('fechaRealizado')
                    ->label('Fecha Realizado')
                    ->date('d/m/Y'),
            ])->paginated(false);
    }
}
