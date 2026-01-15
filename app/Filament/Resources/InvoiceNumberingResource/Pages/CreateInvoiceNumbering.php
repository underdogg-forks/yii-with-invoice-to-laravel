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
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('identifier_format')
                            ->maxLength(255)
                            ->helperText('Use {NUMBER}, {YEAR}, {MONTH} placeholders'),
                        Forms\Components\TextInput::make('left_pad')
                            ->numeric()
                            ->required()
                            ->default(4)
                            ->minValue(0)
                            ->maxValue(10)
                            ->helperText('Number of digits to pad (e.g., 4 = 0001)'),
                        Forms\Components\TextInput::make('next_id')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->label('Next Invoice Number'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
