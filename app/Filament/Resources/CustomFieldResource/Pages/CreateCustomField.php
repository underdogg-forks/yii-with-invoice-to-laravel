<?php

namespace App\Filament\Resources\CustomFieldResource\Pages;

use App\Filament\Resources\CustomFieldResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomField extends CreateRecord
{
    protected static string $resource = CustomFieldResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('CustomField Information')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('field_type')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('table_name')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('required')
                            ->default(false),
                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Textarea::make('options')
                            ->maxLength(65535),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
