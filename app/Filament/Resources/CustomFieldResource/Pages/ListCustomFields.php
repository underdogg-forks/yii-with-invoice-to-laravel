<?php

namespace App\Filament\Resources\CustomFieldResource\Pages;

use App\Filament\Resources\CustomFieldResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListCustomFields extends ListRecords
{
    protected static string $resource = CustomFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
