<?php

namespace App\Filament\Widgets;

use App\Models\MantenimientoGeneral;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MantenimientoGeneralPedidoList extends BaseWidget
{
    // protected static string $view = 'filament.widgets.mantenimientos-list';

    // Opcional: Solo mostrar en panel 'mantenimiento'
    protected static ?string $panel = 'mantenimientoGeneral';

    protected static ?string $heading = 'Mantenimiento a Realizar';
    // Puedes cambiar esto si necesitas mostrar en el dashboard
    protected int|string|array $columnSpan = 'half';
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;

    public function getIngresosProperty()
    {
        return MantenimientoGeneral::latest()->take(10)->get(); // Últimos 5 ingresos
    }

   public function table(Table $table): Table
    {
        return $table
            ->query(
                MantenimientoGeneral::query()->select(['id','fechaSolicitud','tarea','solicitado','prioridad'])->where('reparado','0')->orderBy('fechaSolicitud', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('fechaSolicitud')
                    ->label('Fecha Solicitado')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('tarea')
                    ->label('Tarea'),
                Tables\Columns\TextColumn::make('solicitado')
                    ->label('Solicitado por'),
                Tables\Columns\TextColumn::make('prioridad')
                    ->label('Prioridad Solicitada')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'BAJA' => 'info',
                        'NORMAL' => 'success',
                        'ALTA' => 'warning',
                        'MUY ALTA' => 'danger',
                        default => 'secondary',
                    }),
            ])->paginated(false);
    }
}
