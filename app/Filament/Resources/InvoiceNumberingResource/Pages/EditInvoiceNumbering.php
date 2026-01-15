<?php

namespace App\Filament\Resources\InvoiceNumberingResource\Pages;

use App\Filament\Resources\InvoiceNumberingResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;

class EditInvoiceNumbering extends EditRecord
{
    protected static string $resource = InvoiceNumberingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->before(function () {
                    if ($this->record->invoices()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot delete numbering scheme')
                            ->body('This numbering scheme has associated invoices and cannot be deleted.')
                            ->send();
                        
                        throw new Halt();
                    }
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
