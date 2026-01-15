<?php

namespace App\Filament\Resources\UnitPeppolResource\Pages;

use App\Filament\Resources\UnitPeppolResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUnitPeppol extends CreateRecord
{
    protected static string $resource = UnitPeppolResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
