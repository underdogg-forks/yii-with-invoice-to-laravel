<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Enums\InvoiceStatusEnum;
use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Details')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->columnSpan(2),
                        
                        Forms\Components\Select::make('numbering_id')
                            ->relationship('numbering', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\DatePicker::make('date_created')
                            ->label('Date Created')
                            ->required()
                            ->default(now())
                            ->native(false),
                        
                        Forms\Components\DatePicker::make('date_due')
                            ->label('Due Date')
                            ->required()
                            ->native(false)
                            ->after('date_created'),
                        
                        Forms\Components\Select::make('status')
                            ->options(InvoiceStatusEnum::class)
                            ->required()
                            ->default(InvoiceStatusEnum::Draft),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Invoice Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->columnSpan(2),
                                
                                Forms\Components\Textarea::make('description')
                                    ->maxLength(500)
                                    ->columnSpan(2),
                                
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->minValue(0.01),
                                
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->required()
                                    ->live()
                                    ->prefix('$')
                                    ->minValue(0),
                                
                                Forms\Components\Select::make('tax_rate_id')
                                    ->relationship('taxRate', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                
                                Forms\Components\TextInput::make('discount_percent')
                                    ->numeric()
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(0),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->deleteAction(
                                fn (Forms\Components\Actions\Action $action) => $action
                                    ->requiresConfirmation()
                            ),
                    ]),

                Forms\Components\Section::make('Amounts')
                    ->schema([
                        Forms\Components\TextInput::make('discount_amount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        
                        Forms\Components\TextInput::make('discount_percent')
                            ->numeric()
                            ->suffix('%')
                            ->default(0),
                    ])
                    ->columns(2)
                    ->collapsed(),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('terms')
                            ->maxLength(1000)
                            ->columnSpan(2),
                        
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(1000)
                            ->columnSpan(2),
                    ])
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
