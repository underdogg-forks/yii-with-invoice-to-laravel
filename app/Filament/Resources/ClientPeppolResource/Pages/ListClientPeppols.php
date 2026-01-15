<?php

namespace App\Filament\Resources\ClientPeppolResource\Pages;

use App\Filament\Resources\ClientPeppolResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListClientPeppols extends ListRecords
{
    protected static string $resource = ClientPeppolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
