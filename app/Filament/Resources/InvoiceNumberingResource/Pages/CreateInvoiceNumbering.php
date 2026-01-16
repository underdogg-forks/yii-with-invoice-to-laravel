<?php

namespace App\Filament\Resources\InvoiceNumberingResource\Pages;

use App\Filament\Resources\InvoiceNumberingResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateInvoiceNumbering extends CreateRecord
{
    protected static string $resource = InvoiceNumberingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            // Ensure only one default numbering scheme
            if ($data['is_default'] ?? false) {
                static::getModel()::where('is_default', true)->update(['is_default' => false]);
            }

            return static::getModel()::create($data);
        });
    }
}
