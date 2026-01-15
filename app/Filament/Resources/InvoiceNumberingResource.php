<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceNumberingResource\Pages;
use App\Models\InvoiceNumbering;
use Filament\Resources\Resource;

class InvoiceNumberingResource extends Resource
{
    protected static ?string $model = InvoiceNumbering::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoiceNumberings::route('/'),
            'create' => Pages\CreateInvoiceNumbering::route('/create'),
            'edit' => Pages\EditInvoiceNumbering::route('/{record}/edit'),
            'view' => Pages\ViewInvoiceNumbering::route('/{record}'),
        ];
    }
}
