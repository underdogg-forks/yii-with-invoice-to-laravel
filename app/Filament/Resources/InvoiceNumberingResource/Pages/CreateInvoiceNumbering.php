<?php

namespace App\Filament\Resources\InvoiceNumberingResource\Pages;

use App\Filament\Resources\InvoiceNumberingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoiceNumbering extends CreateRecord
{
    protected static string $resource = InvoiceNumberingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure only one default numbering scheme
        if ($data['is_default'] ?? false) {
            static::getModel()::where('is_default', true)->update(['is_default' => false]);
        }

        return $data;
    }
}
