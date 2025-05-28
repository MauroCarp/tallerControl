<?php

namespace App\Filament\Mantenimiento\Resources;

use App\Filament\Mantenimiento\Resources\MantenimientosResource\Pages;
use App\Filament\Mantenimiento\Resources\MantenimientosResource\RelationManagers;
use App\Models\MantenimientosHerramientas;
use App\Models\Mantenimientosservices;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MantenimientosResource extends Resource
{
    protected static ?string $model = Mantenimientosservices::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Mantenimientos'; // Nombre del enlace
    protected static ?string $breadcrumb = 'Gestión de Mantenimientos';

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
                    ->required(),
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
                        1 => 'Nivel de agua refrigerante',
                        2 => 'Presion de los neumaticos',
                        3 => 'Lubricacion/Engrasado completo',
                        4 => 'Nivel de aceite de motor',
                        5 => 'Nivel de aceite de transmision',
                        6 => 'Nivel de aceite reductoras',
                        7 => 'Limpiado/Sopleteado radiadores y filtro de aire',
                        8 => 'Limpiado/Sopleteado de cabina',
                        9 => 'Lavado del mismo si es necesario',
                    ])
                    ->columns(2)
                    ->columnSpan(2)
                    ->required()
                    ->dehydrateStateUsing(fn ($state) => json_encode($state)),
                Forms\Components\TextInput::make('observaciones')
                    ->label('Observaciones')
                    ->nullable(),
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
                //
            ])
            ->actions([
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
            'index' => Pages\ListMantenimientos::route('/'),
            'create' => Pages\CreateMantenimientos::route('/create'),
            'edit' => Pages\EditMantenimientos::route('/{record}/edit'),
        ];
    }
}
