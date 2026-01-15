<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitPeppolResource\Pages;
use App\Models\UnitPeppol;
use Filament\Resources\Resource;

class UnitPeppolResource extends Resource
{
    protected static ?string $model = UnitPeppol::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Peppol';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'unit_peppol_code';

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnitPeppols::route('/'),
            'create' => Pages\CreateUnitPeppol::route('/create'),
            'edit' => Pages\EditUnitPeppol::route('/{record}/edit'),
            'view' => Pages\ViewUnitPeppol::route('/{record}'),
        ];
    }
}
