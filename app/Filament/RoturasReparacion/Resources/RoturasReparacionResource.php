<?php

namespace App\Filament\RoturasReparacion\Resources;

use App\Filament\RoturasReparacion\Resources\RoturasReparacionResource\Pages;
use App\Filament\RoturasReparacion\Resources\RoturasReparacionResource\RelationManagers;
use App\Models\Reparaciones;
use App\Models\RodadosHerramientas;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TextFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Colors\Color;

class RoturasReparacionResource extends Resource
{
    protected static ?string $model = Reparaciones::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Roturas / Reparaciones'; // Nombre del enlace
    protected static ?string $breadcrumb = 'Roturas / Reparaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->required(),
                Forms\Components\Select::make('rodadoHerramienta_id')
                    ->label('Rodado/Herramienta')
                    ->options(\App\Models\RodadosHerramientas::all()->pluck('nombre', 'id'))
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Rodado / Herramienta')
                            ->required(),
                        Forms\Components\TextInput::make('frecuencia')
                            ->label('Frecuencia')
                            ->default(0)
                            ->helperText('Dejar en 0 si se debe realizar cada vez que se use')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('agenda_dia')
                            ->label('Día de la semana')
                            ->options([
                                'Lunes' => 'Lunes',
                                'Martes' => 'Martes',
                                'Miércoles' => 'Miércoles',
                                'Jueves' => 'Jueves',
                                'Viernes' => 'Viernes',
                                'Sábado' => 'Sábado',
                                'Domingo' => 'Domingo',
                            ])
                            ->dehydrated(false),
                        Forms\Components\Select::make('agenda_turno')
                            ->label('Turno')
                            ->options([
                                'Mañana' => 'Mañana',
                                'Tarde' => 'Tarde',
                            ])
                            ->dehydrated(false),
                        Forms\Components\Hidden::make('agenda')
                            ->dehydrateStateUsing(function ($state, $get) {
                                if(!$get('agenda_dia') || !$get('agenda_turno')) {
                                    return null; // Si no hay día o turno, no se guarda nada
                                }
                                $dia = ucfirst(strtolower($get('agenda_dia')));
                                $turno = ucfirst(strtolower($get('agenda_turno')));
                                return "{'".$dia."':'".$turno."'}";
                            })
                            ->afterStateHydrated(function ($component, $state, $set, $get) {
                                // Opcional: para mantener sincronizado el campo oculto al editar
                                $dia = ucfirst(strtolower($get('agenda_dia')));
                                $turno = ucfirst(strtolower($get('agenda_turno')));
                                if(!$dia || !$turno) {
                                    $set('agenda', null); // Si no hay día o turno, no se guarda nada
                                }else{
                                    $set('agenda', "{'".$dia."':'".$turno."'}");
                                }
                            }),                
                        ])
                    ->createOptionUsing(function (array $data): int {
                        $rodado = RodadosHerramientas::create(['nombre' => $data['nombre'],
                            'frecuencia' => $data['frecuencia'],
                            'agenda' => $data['agenda'],
                        ]);
                        return $rodado->id;
                    }),
                Forms\Components\TextInput::make('encargado')
                    ->label('Encargado')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('descripcion')
                    ->label('Descripción de la Rotura')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('operario')
                    ->label('Operario a Cargo')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('descripcionReparacion')
                    ->label('Descripción de la Reparación')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('tipo')
                    ->label('Tipo de Trabajo')
                    ->default('Propio')
                    ->options([
                        'Propio' => 'Propio',
                        'Tercierizado' => 'Tercierizado',
                    ])
                    ->reactive()
                    ->searchable(),
                Forms\Components\TextInput::make('importe')
                    ->label('Importe')
                    ->numeric()
                    ->reactive()
                    ->visible(fn ($get) => $get('tipo') === 'Tercerizado'),
                Forms\Components\TextInput::make('horas')
                    ->label('Horas')
                    ->numeric()
                    ->reactive()
                    ->visible(fn ($get) => $get('tipo') !== 'Tercerizado'),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('encargado')
                    ->label('Encargado')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rodadoHerramienta_id')
                    ->label('Rodado/Herramienta')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                    return \App\Models\RodadosHerramientas::find($state)?->nombre ?? '';
                }),
                Tables\Columns\TextColumn::make('operario')
                    ->label('Operario a Cargo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo de Trabajo')
                    ->searchable()
                    ->sortable(),
            ])
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

                Tables\Filters\SelectFilter::make('rodadoHerramienta_id')
                    ->label('Rodado/Herramienta')
                    ->options(\App\Models\RodadosHerramientas::all()->pluck('nombre', 'id')->toArray()),

                Tables\Filters\Filter::make('encargado')
                    ->label('Encargado')
                    ->form([
                        Forms\Components\TextInput::make('value')->label('Encargado'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['value'], fn ($q, $value) => $q->where('encargado', 'like', '%' . $value . '%'));
                    }),

                Tables\Filters\Filter::make('operario')
                    ->label('Operario a Cargo')
                    ->form([
                        Forms\Components\TextInput::make('value')->label('Operario a Cargo'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['value'], fn ($q, $value) => $q->where('operario', 'like', '%' . $value . '%'));
                    }),

                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo de Trabajo')
                    ->options([
                        'Propio' => 'Propio',
                        'Tercierizado' => 'Tercierizado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->label('')->color(Color::Indigo),
                Tables\Actions\EditAction::make()
                ->label(''),
            ])
            ->headerActions([
                Tables\Actions\Action::make('download_filtered_pdf')
                    ->label('Reporte Filtrado')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Tables\Actions\Action $action) {
                        // Obtener los filtros seleccionados
                        $filters = $action->getTable()->getFilters();
                 
                        // Construir la consulta base
                        $query = \App\Models\Reparaciones::query();

                        // Aplicar filtros manualmente según los valores seleccionados
                        if (!empty($filters['fecha']->getState()[0])) {
                            $query->whereDate('fecha', '>=', $filters['fecha']->getState()[0]);
                        }
                        if (!empty($filters['fecha']->getState()[1])) {
                            $query->whereDate('fecha', '<=', $filters['fecha']->getState()[1]);
                        }

                        if (!empty($filters['rodadoHerramienta_id']->getState()['value'])) {
                            $query->where('rodadoHerramienta_id', $filters['rodadoHerramienta_id']->getState()['value']);
                        }

                        if (!empty($filters['encargado']->getState()['value'])) {
                            $query->where('encargado', 'like', '%' . $filters['encargado']->getState()['value'] . '%');
                        }

                        if (!empty($filters['operario']->getState()['value'])) {
                            $query->where('operario', 'like', '%' . $filters['operario']->getState()['value'] . '%');
                        }
                        if (!empty($filters['tipo']->getState()['value'])) {
                            $query->where('tipo', $filters['tipo']->getState()['value']);
                        }

                        $query->orderBy('fecha', 'desc');

                        // Obtener los registros filtrados
                        $records = $query->get();
                        $pdf = Pdf::loadView('pdf.roturasReparaciones', ['records' => $records])->setPaper('a4', 'landscape');
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'Reporte_RoturasReparaciones_Filtrado.pdf'
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
    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y'),
                TextEntry::make('rodadoHerramienta_id')
                    ->label('Rodado/Herramienta')
                    ->formatStateUsing(function ($state) {
                        return RodadosHerramientas::find($state)?->nombre ?? '';
                    }),
                TextEntry::make('encargado')
                    ->label('Encargado'),
                TextEntry::make('descripcion')
                    ->label('Descripción de la Rotura'),
                TextEntry::make('operario')
                    ->label('Operario a Cargo'),
                TextEntry::make('descripcionReparacion')
                    ->label('Descripción de la Reparación'),
                TextEntry::make('tipo')
                    ->label('Tipo de Trabajo'),
                TextEntry::make('importe')
                    ->label('Importe')
                    ->visible(fn ($record) => $record->tipo === 'Tercerizado'),
                TextEntry::make('horas')
                    ->label('Horas')
                    ->visible(fn ($record) => $record->tipo !== 'Tercerizado'),
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
            'index' => Pages\ListRoturasReparacions::route('/'),
            'create' => Pages\CreateRoturasReparacion::route('/create'),
            'edit' => Pages\EditRoturasReparacion::route('/{record}/edit'),
        ];
    }
}
