<?php

namespace App\Filament\Resources\InvoiceNumberingResource\Pages;

use App\Filament\Resources\InvoiceNumberingResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoiceNumbering extends CreateRecord
{
    protected static string $resource = InvoiceNumberingResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('InvoiceNumbering Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('identifier_format')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('left_pad')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('next_id')
                            ->relationship('next', 'name')
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
