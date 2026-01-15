<?php

namespace App\Filament\Resources\PaymentPeppolResource\Pages;

use App\Filament\Resources\PaymentPeppolResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditPaymentPeppol extends EditRecord
{
    protected static string $resource = PaymentPeppolResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('PaymentPeppol Information')
                    ->schema([
                        Forms\Components\Select::make('inv_id')
                            ->relationship('invoice', 'invoice_number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Invoice'),
                        Forms\Components\TextInput::make('payment_means_code')
                            ->required()
                            ->maxLength(10)
                            ->helperText('Peppol payment means code'),
                        Forms\Components\TextInput::make('payment_id')
                            ->maxLength(255)
                            ->label('Payment ID')
                            ->helperText('Payment identifier'),
                        Forms\Components\TextInput::make('account_id')
                            ->maxLength(255)
                            ->label('Account ID')
                            ->helperText('Bank account identifier'),
                        Forms\Components\TextInput::make('network_id')
                            ->maxLength(255)
                            ->label('Network ID')
                            ->helperText('Payment network identifier'),
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
