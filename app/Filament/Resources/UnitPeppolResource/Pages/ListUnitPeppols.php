<?php

namespace App\Filament\Resources\UnitPeppolResource\Pages;

use App\Filament\Resources\UnitPeppolResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListUnitPeppols extends ListRecords
{
    protected static string $resource = UnitPeppolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
