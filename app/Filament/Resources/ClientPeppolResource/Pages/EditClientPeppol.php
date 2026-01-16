<?php

namespace App\Filament\Resources\ClientPeppolResource\Pages;

use App\Filament\Resources\ClientPeppolResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientPeppol extends EditRecord
{
    protected static string $resource = ClientPeppolResource::class;

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
