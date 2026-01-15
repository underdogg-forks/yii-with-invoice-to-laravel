<?php

namespace App\Filament\Resources\ClientPeppolResource\Pages;

use App\Filament\Resources\ClientPeppolResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewClientPeppol extends ViewRecord
{
    protected static string $resource = ClientPeppolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
