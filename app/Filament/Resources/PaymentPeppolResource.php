<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentPeppolResource\Pages;
use App\Models\PaymentPeppol;
use Filament\Resources\Resource;

class PaymentPeppolResource extends Resource
{
    protected static ?string $model = PaymentPeppol::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Peppol';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'payment_means_code';

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentPeppols::route('/'),
            'create' => Pages\CreatePaymentPeppol::route('/create'),
            'edit' => Pages\EditPaymentPeppol::route('/{record}/edit'),
            'view' => Pages\ViewPaymentPeppol::route('/{record}'),
        ];
    }
}
