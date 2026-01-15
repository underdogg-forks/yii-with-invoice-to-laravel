<?php

namespace App\Filament\Resources\PaymentPeppolResource\Pages;

use App\Filament\Resources\PaymentPeppolResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentPeppol extends EditRecord
{
    protected static string $resource = PaymentPeppolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
