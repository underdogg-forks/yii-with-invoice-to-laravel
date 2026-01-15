<?php

namespace App\Filament\Resources\InvoiceNumberingResource\Pages;

use App\Filament\Resources\InvoiceNumberingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoiceNumberings extends ListRecords
{
    protected static string $resource = InvoiceNumberingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
