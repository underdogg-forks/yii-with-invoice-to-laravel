<?php

namespace App\Filament\Resources\ClientPeppolResource\Pages;

use App\Filament\Resources\ClientPeppolResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateClientPeppol extends CreateRecord
{
    protected static string $resource = ClientPeppolResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ClientPeppol Information')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('electronic_address')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('electronic_address_scheme')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('legal_entity_registration_name')
                            ->maxLength(255),
                        Forms\Components\Select::make('legal_entity_company_id')
                            ->relationship('legal_entity_company', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
