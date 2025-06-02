<?php

namespace App\Filament\Service\Resources;

use App\Filament\Service\Resources\ServicesResource\Pages;
use App\Filament\Service\Resources\ServicesResource\RelationManagers;
use App\Models\Mantenimientosservices;
use App\Models\RodadosHerramientas;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServicesResource extends Resource
{
    protected static ?string $model = Mantenimientosservices::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Services'; // Nombre del enlace
    protected static ?string $breadcrumb = 'Gestión de Services';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->required(),

                Forms\Components\TextInput::make('responsable')
                    ->label('Responsables')
                    ->required(),

                Forms\Components\Select::make('turno')
                    ->label('Turno')
                    ->options([
                        'Mañana' => 'Mañana',
                        'Tarde' => 'Tarde',
                    ])
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
                Forms\Components\TextInput::make('horasMotor')
                    ->label('Horas Motor')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('km')
                    ->label('Kilómetros')
                    ->numeric()
                    ->required(),
                Forms\Components\CheckboxList::make('tareas')
                    ->label('Tareas')
                    ->options([
                        1 => 'Cambio Aceite de Motor',
                        2 => 'Cambio de Filtro de Aceite',
                        3 => 'Cambio de Filtro de Combustible',
                        4 => 'Cambio de Filtro de Aire',
                        5 => 'Cambio de Filtro de Habitaculo',
                        6 => 'Cambio de Aceite de Transmision',
                    ])
                    ->columns(2)
                    ->columnSpan(2)
                    ->required()
                    ->dehydrateStateUsing(fn ($state) => json_encode($state)),
                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->nullable(),
                Forms\Components\Hidden::make('isMantenimiento')
                    ->default(0),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getModel()::query()->where('isMantenimiento', '0'))
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('rodadoHerramienta_id')
                    ->label('Rodado/Herramienta')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return \App\Models\RodadosHerramientas::find($state)?->nombre ?? '';
                    }),
                Tables\Columns\TextColumn::make('responsable')
                    ->label('Responsables')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('fecha', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('fecha', '<=', $data['until']));
                    }),
                    
            ])
            ->actions([
            Tables\Actions\Action::make('download_pdf')
                ->label('Reporte')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function ($record) {
                    $pdf = Pdf::loadView('pdf.service', ['record' => $record]);
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'Reporte_Mantenimiento'.$record->fecha.'.pdf'
                    );
                }),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateServices::route('/create'),
            'edit' => Pages\EditServices::route('/{record}/edit'),
        ];
    }
}
