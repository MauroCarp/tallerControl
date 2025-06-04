<?php

namespace App\Filament\Mantenimiento\Resources;

use App\Filament\Mantenimiento\Resources\RodadosHerramientasResource\Pages;
use App\Filament\Mantenimiento\Resources\RodadosHerramientasResource\RelationManagers;
use App\Models\RodadosHerramientas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Support\Colors\Color;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RodadosHerramientasResource extends Resource
{
    protected static ?string $model = RodadosHerramientas::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Rodados / Herramientas'; // Nombre del enlace

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Rodado / Herramienta')
                    ->required(),
                Forms\Components\TextInput::make('frecuencia')
                    ->label('Frecuencia de mantenimiento (en días)')
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
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('serviceHoras')
                        ->label('Frecuencia de services')
                        ->default(0)
                        ->numeric()
                        ->required(),
                    Forms\Components\Select::make('unidadService')
                        ->label('Unidad')
                        ->options([
                            'Km' => 'Kilómetros',
                            'Horas' => 'Horas',
                            'Dias' => 'Días',
                            'Meses' => 'Meses',
                        ])
                        ->required(),
                ])
                ->columnSpan(2)
                ->columns(2),
                
            ])
            ->columns(4);
        }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Rodado / Herramienta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('frecuencia')
                    ->label('Frecuencia de Mantenimiento')
                    ->formatStateUsing(function ($state) { 

                        if($state == 0) {

                            return 'Cada vez que se use';
                            
                        } else {

                            return $state . ' días';

                        }
                        
                    }),
                Tables\Columns\TextColumn::make('agenda')
                    ->label('Agenda')
                    ->formatStateUsing(function ($state) {
                        // Espera formato: {'Día':'Turno'}
                        $agenda = json_decode(str_replace("'",'"',$state), true);

                        if (is_null($agenda)) {
                            return '';
                        }


                        $diaKey = array_key_first($agenda);
                        $turno = $agenda[$diaKey] ?? '';
                        return $diaKey . ' de la ' . $turno;
                    }),
                Tables\Columns\TextColumn::make('agenda')
                    ->label('Agenda')
                    ->formatStateUsing(function ($state) {
                        // Espera formato: {'Día':'Turno'}
                        $agenda = json_decode(str_replace("'",'"',$state), true);

                        if (is_null($agenda)) {
                            return '';
                        }


                        $diaKey = array_key_first($agenda);
                        $turno = $agenda[$diaKey] ?? '';
                        return $diaKey . ' de la ' . $turno;
                    }),
                Tables\Columns\TextColumn::make('serviceHoras')
                    ->label('Frecuencia de Service')
                    ->formatStateUsing(function ($state, $record) {
                        if($state == 0) {
                            return '';
                        } else {
                            return number_format($state,0,',','.') . ' ' . $record->unidadService;
                        }
                    }),
            ])
            ->filters([
                //  
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
                TextEntry::make('nombre')
                    ->label('Rodado / Herramienta'),
                TextEntry::make('frecuencia')
                    ->label('Frecuencia de mantenimiento (en días)')
                    ->formatStateUsing(function ($state) {
                        return $state == 0 ? 'Cada vez que se use' : $state . ' días';
                    }),
                TextEntry::make('agenda')
                    ->label('Agenda')
                    ->formatStateUsing(function ($state) {
                        $agenda = json_decode(str_replace("'",'"',$state), true);
                        if (is_null($agenda)) {
                            return '';
                        }
                        $diaKey = array_key_first($agenda);
                        $turno = $agenda[$diaKey] ?? '';
                        return $diaKey . ' de la ' . $turno;
                    }),
                TextEntry::make('serviceHoras')
                    ->label('Frecuencia de services')
                    ->formatStateUsing(function ($state, $record) {
                        if ($state == 0) {
                            return '';
                        }
                        return number_format($state, 0, ',', '.') . ' ' . $record->unidadService;
                    }),
                TextEntry::make('unidadService')
                    ->label('Unidad')
                    ->formatStateUsing(function ($state) {
                        $map = [
                            'Km' => 'Kilómetros',
                            'Horas' => 'Horas',
                            'Dias' => 'Días',
                            'Meses' => 'Meses',
                        ];
                        return $map[$state] ?? $state;
                    }),
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
            'index' => Pages\ListRodadosHerramientas::route('/'),
            'create' => Pages\CreateRodadosHerramientas::route('/create'),
            'edit' => Pages\EditRodadosHerramientas::route('/{record}/edit'),
        ];
    }
}
