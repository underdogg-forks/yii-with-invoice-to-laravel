<?php

namespace App\Filament\Resources\TemplateResource\Pages;

use App\Filament\Resources\TemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemplate extends EditRecord
{
    protected static string $resource = TemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->action(function () {
                    if (!$this->record->canBeDeleted()) {
                        throw new \Exception('Cannot delete default template.');
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
        // Ensure only one default template per category
        if ($data['is_default'] ?? false) {
            static::getModel()::where('id', '!=', $this->record->id)
                ->where('category', $data['category'])
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        return $data;
    }
}
