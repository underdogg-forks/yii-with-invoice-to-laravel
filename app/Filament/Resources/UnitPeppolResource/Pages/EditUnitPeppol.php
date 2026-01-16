<?php

namespace App\Filament\Resources\UnitPeppolResource\Pages;

use App\Filament\Resources\UnitPeppolResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitPeppol extends EditRecord
{
    protected static string $resource = UnitPeppolResource::class;

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
