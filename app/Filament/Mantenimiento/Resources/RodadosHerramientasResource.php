<?php

namespace App\Filament\Mantenimiento\Resources;

use App\Filament\Mantenimiento\Resources\RodadosHerramientasResource\Pages;
use App\Filament\Mantenimiento\Resources\RodadosHerramientasResource\RelationManagers;
use App\Models\RodadosHerramientas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
                    ->label('Frecuencia')
                    ->default(0)
                    ->helperText('Dejar en 0 si se debe realizar cada vez que se use')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('agenda_turno')
                    ->label('Turno')
                    ->options([
                        'Mañana' => 'Mañana',
                        'Tarde' => 'Tarde',
                    ])
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
            ])
            ->columns(4);
        }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListRodadosHerramientas::route('/'),
            'create' => Pages\CreateRodadosHerramientas::route('/create'),
            'edit' => Pages\EditRodadosHerramientas::route('/{record}/edit'),
        ];
    }
}
