<?php

namespace App\Filament\Resources\TemplateResource\Pages;

use App\Filament\Resources\TemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTemplate extends CreateRecord
{
    protected static string $resource = TemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set created_by to current user
        $data['created_by'] = auth()->id();

        // Ensure only one default template per category
        if ($data['is_default'] ?? false) {
            static::getModel()::where('category', $data['category'])
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        return $data;
    }
}
