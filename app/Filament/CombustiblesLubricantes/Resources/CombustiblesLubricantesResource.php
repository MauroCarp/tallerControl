<?php

namespace App\Filament\CombustiblesLubricantes\Resources;

use App\Filament\CombustiblesLubricantes\Resources\CombustiblesLubricantesResource\Pages;
use App\Filament\CombustiblesLubricantes\Resources\CombustiblesLubricantesResource\RelationManagers;
use App\Models\Combustibles;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CombustiblesLubricantesResource extends Resource
{
    protected static ?string $model = Combustibles::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Combustibles / Lubricantes'; // Nombre del enlace
    protected static ?string $breadcrumb = 'Combustibles / Lubricantes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->required(),
                Forms\Components\Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'Nafta' => 'Nafta',
                        'Gasoil' => 'Gasoil',
                        'Lubricante' => 'Lubricante',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('ingresoLitros')
                    ->label('Ingreso Litros')
                    ->default(0)
                    ->numeric(),
                Forms\Components\TextInput::make('origen')
                    ->label('Origen'),
                Forms\Components\TextInput::make('egresoLitros')
                    ->label('Egreso Litros')
                    ->default(0)
                    ->numeric(),
                Forms\Components\Select::make('destino')
                    ->label('Destino')
                    ->options(\App\Models\RodadosHerramientas::all()->pluck('nombre', 'id')->toArray()),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->searchable()  
                    ->date('d-m-Y'),
                Tables\Columns\TextColumn::make('tipo')
                    ->searchable()  
                    ->label('Tipo'),
                Tables\Columns\TextColumn::make('ingresoLitros')
                    ->label('Ingreso Litros')
                    ->getStateUsing(function($record){
                        return $record->ingresoLitros . ' Lts';
                    }),
                Tables\Columns\TextColumn::make('origen')
                    ->label('Origen'),
                Tables\Columns\TextColumn::make('egresoLitros')
                    ->label('Egreso Litros')
                    ->getStateUsing(function($record){
                        return $record->egresoLitros . ' Lts';
                    }),
                Tables\Columns\TextColumn::make('destino')
                    ->getStateUsing(function ($record) {
                        return optional(\App\Models\RodadosHerramientas::find($record->destino))->nombre;
                    })
                    ->searchable()  
                    ->label('Destino'),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_desde')->label('Desde'),
                        Forms\Components\DatePicker::make('fecha_hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['fecha_desde'], fn ($q) => $q->whereDate('fecha', '>=', $data['fecha_desde']))
                            ->when($data['fecha_hasta'], fn ($q) => $q->whereDate('fecha', '<=', $data['fecha_hasta']));
                    }),

                Tables\Filters\SelectFilter::make('destino')
                    ->label('Rodado/Herramienta')
                    ->options(\App\Models\RodadosHerramientas::all()->pluck('nombre', 'id')->toArray()),
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'Nafta' => 'Nafta',
                        'Gasoil' => 'Gasoil',
                        'Lubricante' => 'Lubricante',
                    ]),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('download_filtered_pdf')
                    ->label('Reporte Filtrado')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Tables\Actions\Action $action) {
                        // Obtener los filtros seleccionados
                        $filters = $action->getTable()->getFilters();
                 
                        // Construir la consulta base
                        $query = \App\Models\Combustibles::query();

                        // Aplicar filtros manualmente según los valores seleccionados
                        if (!empty($filters['fecha']->getState()[0])) {
                            $query->whereDate('fecha', '>=', $filters['fecha']->getState()[0]);
                        }
                        if (!empty($filters['fecha']->getState()[1])) {
                            $query->whereDate('fecha', '<=', $filters['fecha']->getState()[1]);
                        }
                        if (!empty($filters['destino']->getState()['value'])) {
                            $query->where('destino', $filters['destino']->getState()['value']);
                        }
                        if (!empty($filters['tipo']->getState()['value'])) {
                            $query->where('tipo', $filters['tipo']->getState()['value']);
                        }

                        $query->orderBy('fecha', 'desc');

                        // Obtener los registros filtrados
                        $records = $query->get();
                        $pdf = Pdf::loadView('pdf.combustiblesLubricantes', ['records' => $records])->setPaper('a4', 'portrait');
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'Reporte_CombustiblesLubricantes_Filtrado.pdf'
                        );
                    }),
                Tables\Actions\CreateAction::make()->label('Nuevo Registro'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCombustiblesLubricantes::route('/'),
            'create' => Pages\CreateCombustiblesLubricantes::route('/create'),
            'edit' => Pages\EditCombustiblesLubricantes::route('/{record}/edit'),
        ];
    }
}
