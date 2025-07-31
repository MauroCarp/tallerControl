<?php

namespace App\Filament\MantenimientoGeneral\Resources;

use App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource\Pages;
use App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource\RelationManagers;
use App\Models\MantenimientoGeneral;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MantenimientoGeneralResource extends Resource
{
    protected static ?string $model = MantenimientoGeneral::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Mantenimiento General'; // Nombre del enlace
    protected static ?string $breadcrumb = 'Gestión de Mantenimientos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Solicitud')
                        ->schema([
                            Forms\Components\DatePicker::make('fechaSolicitud')->required(),
                            Forms\Components\Textarea::make('tarea')->required(),
                            Forms\Components\Select::make('prioridad')
                                ->label('Prioridad')
                                ->required()
                                ->options([
                                    'BAJA' => 'BAJA',
                                    'NORMAL' => 'NORMAL',
                                    'ALTA' => 'ALTA',
                                    'MUY ALTA' => 'MUY ALTA',
                                ]),
                            Forms\Components\TextInput::make('solicitado')
                                ->label('Sector')
                                ->readOnly()
                                ->default(function () {
                                    $userEmail = auth()->user()?->email;
                                    switch ($userEmail) {
                                        case 'ruben@barloventosrl.website':
                                            return 'Gerencial';
                                        case 'mauro@mauro.com':
                                            return 'Gerencial';
                                        case 'ornela@barloventosrl.website':
                                            return 'Produccion';
                                        case 'mariano@barloventosrl.website':
                                            return 'Administracion';
                                        case 'carlos@barloventosrl.website':
                                            return 'Mantenimiento';
                                        // Agrega más casos según sea necesario
                                        default:
                                            return '';
                                    }
                                }),
                        ]),
                    Forms\Components\Wizard\Step::make('Realización')
                        ->schema([
                            Forms\Components\Checkbox::make('reparado')
                                ->default(0)
                                ->dehydrateStateUsing(fn ($state) => $state ? 1 : 0),
                            Forms\Components\TextInput::make('realizado')->label('Realizado por'),
                            Forms\Components\DatePicker::make('fechaRealizado'),
                            Forms\Components\TextInput::make('horas')->numeric(),
                            Forms\Components\Textarea::make('materiales'),
                            Forms\Components\TextInput::make('costo')->numeric(),
                    ])->hidden(fn (string $context) => $context === 'create')->columns(3),
                ])->maxWidth('md')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fechaSolicitud')->label('Fecha Solicitud')->date('d-m-Y'),
                Tables\Columns\TextColumn::make('tarea')->label('Tarea')->limit(50),
                Tables\Columns\TextColumn::make('solicitado')->label('Solicitado por'),
                Tables\Columns\IconColumn::make('reparado')
                    ->label('Reparado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('realizado')->label('Realizado por'),
                Tables\Columns\TextColumn::make('horas')->label('Horas'),
                Tables\Columns\TextColumn::make('materiales')->label('Materiales')->limit(50),
                Tables\Columns\TextColumn::make('costo')->label('Costo'),
                Tables\Columns\TextColumn::make('fechaRealizado')->label('Fecha Realizado')->date(),
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
            'index' => Pages\ListMantenimientoGeneral::route('/'),
            // 'create' => Pages\CreateMantenimientoGeneral::route('/create'),
            // 'edit' => Pages\EditMantenimientoGeneral::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {

        $panelId = filament()->getCurrentPanel()->getId();

        // Solo mostrar en el panel 'mantenimientoGeneral' (ajusta el ID según tu configuración)
        return $panelId === 'mantenimientoGeneral';
    }
}
