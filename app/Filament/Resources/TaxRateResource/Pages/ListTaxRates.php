<?php

namespace App\Filament\Resources\TaxRateResource\Pages;

use App\Filament\Resources\TaxRateResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListTaxRates extends ListRecords
{
    protected static string $resource = TaxRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
