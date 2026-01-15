<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientPeppolResource\Pages;
use App\Models\ClientPeppol;
use Filament\Resources\Resource;

class ClientPeppolResource extends Resource
{
    protected static ?string $model = ClientPeppol::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Peppol';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'electronic_address';

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientPeppols::route('/'),
            'create' => Pages\CreateClientPeppol::route('/create'),
            'edit' => Pages\EditClientPeppol::route('/{record}/edit'),
            'view' => Pages\ViewClientPeppol::route('/{record}'),
        ];
    }
}
