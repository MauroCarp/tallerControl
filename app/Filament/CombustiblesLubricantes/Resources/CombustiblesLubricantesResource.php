<?php

namespace App\Filament\CombustiblesLubricantes\Resources;

use App\Filament\CombustiblesLubricantes\Resources\CombustiblesLubricantesResource\Pages;
use App\Filament\CombustiblesLubricantes\Resources\CombustiblesLubricantesResource\RelationManagers;
use App\Models\CombustiblesLubricantes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CombustiblesLubricantesResource extends Resource
{
    protected static ?string $model = CombustiblesLubricantes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
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
            'index' => Pages\ListCombustiblesLubricantes::route('/'),
            'create' => Pages\CreateCombustiblesLubricantes::route('/create'),
            'edit' => Pages\EditCombustiblesLubricantes::route('/{record}/edit'),
        ];
    }
}
