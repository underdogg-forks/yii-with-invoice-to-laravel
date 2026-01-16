<?php

namespace App\Filament\Resources\PaymentPeppolResource\Pages;

use App\Filament\Resources\PaymentPeppolResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListPaymentPeppols extends ListRecords
{
    protected static string $resource = PaymentPeppolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
