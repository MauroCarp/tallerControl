<?php

namespace App\Filament\MantenimientoGeneral\Resources;

use App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource\Pages;
use App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource\RelationManagers;
use App\Models\MantenimientoGeneral;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MantenimientoGeneralResource extends Resource
{

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
                ])->hidden(function (string $context, ?MantenimientoGeneral $record) {
                    if ($context !== 'edit' || !$record) {
                        return false; // Mostrar en creación
                    }
                    
                    $userEmail = auth()->user()?->email;

                    // Mostrar el paso "Solicitud" a todos los usuarios,
                    // pero los usuarios de mantenimiento solo lo ven si el campo solicitado es 'Mantenimiento'
                    $userSector = match($userEmail) {
                        'carlos@barloventosrl.website' => 'Mantenimiento',
                        'leandro@barloventosrl.website' => 'Mantenimiento',
                        'federico@barloventosrl.website' => 'Mantenimiento',
                        default => ''
                    };

                    if ($userSector === 'Mantenimiento') {
                        return $record->solicitado !== 'Mantenimiento';
                    }

                    return false; // Otros usuarios siempre pueden ver el paso
                }),
                Forms\Components\Wizard\Step::make('Realización')
                ->schema([
                    
                    Forms\Components\DatePicker::make('fechaRealizar')->label('Fecha a Realizar'),
                    Forms\Components\TextInput::make('prioridad_orden')
                        ->label('Orden de Prioridad')
                        ->numeric()
                        ->minValue(1)
                        ->helperText('Las tareas existentes se reorganizarán automáticamente'),
                    Forms\Components\Select::make('realizado')->label('A realizar por')
                    ->options([
                        '9' => 'Federico',
                        '5' => 'Luciano',
                        '1' => 'Test',
                    ]),
                    Forms\Components\DatePicker::make('fechaRealizado'),
                    Forms\Components\TextInput::make('horas')->numeric(),
                    Forms\Components\Textarea::make('materiales'),
                    Forms\Components\TextInput::make('costo')->numeric(),
                    Forms\Components\Checkbox::make('reparado')
                    ->default(0)
                    ->dehydrateStateUsing(fn ($state) => $state ? 1 : 0),
                ])
                ->hidden(function (string $context) {
                    // Solo mostrar en edición y si el usuario tiene el rol adecuado
                    if ($context !== 'edit') {
                    return true;
                    }
                    $user = auth()->user();
                    // Cambia 'admin' por el nombre del rol que desees
                    return !$user || !$user->hasRole('mantenimiento');
                })
                ->columns(3),
            ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('reparado', 0))
            ->columns([
            Tables\Columns\TextColumn::make('fechaSolicitud')->label('Fecha Solicitud')->date('d-m-Y'),
            Tables\Columns\TextColumn::make('tarea')->label('Tarea')->limit(50),
            Tables\Columns\TextColumn::make('solicitado')->label('Solicitado por'),
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
            Tables\Columns\IconColumn::make('reparado')
                ->label('Realizado')
                ->boolean()
                ->trueIcon('heroicon-o-check')
                ->falseIcon('heroicon-o-x-mark')
                ->trueColor('success')
                ->falseColor('danger'),
            Tables\Columns\TextColumn::make('fechaRealizar')->label('Fecha a Realizar')->date('d-m-Y'),
            Tables\Columns\TextColumn::make('prioridad_orden')
                ->label('Orden Prioridad de Trabajo')
                ->sortable()
                ->badge()
                ->color('info'),
            // Tables\Columns\TextColumn::make('realizado')->label('Realizado por'),
            // Tables\Columns\TextColumn::make('horas')->label('Horas'),
            // Tables\Columns\TextColumn::make('materiales')->label('Materiales')->limit(50),
            // Tables\Columns\TextColumn::make('costo')->label('Costo'),
            // Tables\Columns\TextColumn::make('fechaRealizado')->label('Fecha Realizado')->date(),
            ])
            ->filters([
            //
            ])
            ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
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
                TextEntry::make('fechaSolicitud')
                    ->label('Fecha Solicitud')
                    ->date('d-m-Y')
                    ->size('lg'),
                TextEntry::make('tarea')
                    ->label('Tarea')
                    ->size('lg'),
                TextEntry::make('solicitado')
                    ->label('Solicitado por')
                    ->size('lg'),
                TextEntry::make('prioridad')
                    ->label('Prioridad Solicitada')
                    ->size('lg'),
                TextEntry::make('fechaRealizar')
                    ->label('Fecha a Realizar')
                    ->date('d-m-Y')
                    ->size('lg'),
                TextEntry::make('prioridad_orden')
                    ->label('Orden Prioridad de Trabajo')
                    ->size('lg'),
                TextEntry::make('realizado')
                    ->label('A realizar por')
                    ->size('lg'),
                TextEntry::make('fechaRealizado')
                    ->label('Fecha Realizado')
                    ->date('d-m-Y')
                    ->size('lg'),
                TextEntry::make('horas')
                    ->label('Horas')
                    ->size('lg'),
                TextEntry::make('materiales')
                    ->label('Materiales')
                    ->size('lg'),
                TextEntry::make('costo')
                    ->label('Costo')
                    ->size('lg'),
                IconEntry::make('reparado')
                    ->label('Realizado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->size('lg'),
            ])
            ->columns(3);
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
            'view' => Pages\ViewMantenimientoGeneral::route('/{record}'),
            'edit' => Pages\EditMantenimientoGeneral::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {

        $panelId = filament()->getCurrentPanel()->getId();

        // Solo mostrar en el panel 'mantenimientoGeneral' (ajusta el ID según tu configuración)
        return $panelId === 'mantenimientoGeneral';
    }
}
