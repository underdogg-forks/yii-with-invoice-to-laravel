<?php

namespace App\Filament\Resources\UnitPeppolResource\Pages;

use App\Filament\Resources\UnitPeppolResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUnitPeppol extends ViewRecord
{
    protected static string $resource = UnitPeppolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
