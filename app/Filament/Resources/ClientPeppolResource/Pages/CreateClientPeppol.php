<?php

namespace App\Filament\Resources\ClientPeppolResource\Pages;

use App\Filament\Resources\ClientPeppolResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClientPeppol extends CreateRecord
{
    protected static string $resource = ClientPeppolResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
