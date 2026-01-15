<?php

namespace App\Filament\Resources\UnitPeppolResource\Pages;

use App\Filament\Resources\UnitPeppolResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateUnitPeppol extends CreateRecord
{
    protected static string $resource = UnitPeppolResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('UnitPeppol Information')
                    ->schema([
                        Forms\Components\Select::make('unit_id')
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('unit_peppol_code')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('unit_peppol_name')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
