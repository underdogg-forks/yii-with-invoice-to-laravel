<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Filament\Resources\SalesOrderResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditSalesOrder extends EditRecord
{
    protected static string $resource = SalesOrderResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('SalesOrder Information')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('quote_id')
                            ->relationship('quote', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('order_number')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('order_date')
                            ->native(false),
                        Forms\Components\DatePicker::make('expected_delivery_date')
                            ->native(false),
                        Forms\Components\TextInput::make('status')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()->prefix('$'),
                        Forms\Components\TextInput::make('tax_total')
                            ->numeric()->prefix('$'),
                        Forms\Components\TextInput::make('discount_amount')
                            ->numeric()->prefix('$'),
                        Forms\Components\TextInput::make('discount_percent')
                            ->numeric()->suffix('%'),
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()->prefix('$'),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535),
                    ])
                    ->columns(2),
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
