<?php

namespace App\Filament\Resources\PaymentPeppolResource\Pages;

use App\Filament\Resources\PaymentPeppolResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentPeppol extends ViewRecord
{
    protected static string $resource = PaymentPeppolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
