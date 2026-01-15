<?php

namespace App\Filament\Resources\PaymentPeppolResource\Pages;

use App\Filament\Resources\PaymentPeppolResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentPeppol extends CreateRecord
{
    protected static string $resource = PaymentPeppolResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
