<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Client Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('surname')
                            ->maxLength(151),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\TextInput::make('mobile')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\Toggle::make('active')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\TextInput::make('address_1')
                            ->maxLength(100),
                        
                        Forms\Components\TextInput::make('address_2')
                            ->maxLength(100),
                        
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100),
                        
                        Forms\Components\TextInput::make('zip')
                            ->maxLength(10),
                        
                        Forms\Components\TextInput::make('country')
                            ->maxLength(30),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Tax Information')
                    ->schema([
                        Forms\Components\TextInput::make('vat_id')
                            ->label('VAT ID')
                            ->maxLength(30),
                        
                        Forms\Components\TextInput::make('tax_code')
                            ->maxLength(20),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
