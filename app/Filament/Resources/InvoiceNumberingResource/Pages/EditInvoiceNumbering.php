<?php

namespace App\Filament\Resources\InvoiceNumberingResource\Pages;

use App\Filament\Resources\InvoiceNumberingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceNumbering extends EditRecord
{
    protected static string $resource = InvoiceNumberingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->action(function () {
                    if ($this->record->invoices()->count() > 0) {
                        throw new \Exception('Cannot delete numbering scheme with associated invoices.');
                    }
                    $this->record->delete();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure only one default numbering scheme
        if ($data['is_default'] ?? false) {
            static::getModel()::where('id', '!=', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        return $data;
    }
}
