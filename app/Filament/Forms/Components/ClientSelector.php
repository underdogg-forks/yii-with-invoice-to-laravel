<?php

namespace App\Filament\Forms\Components;

use App\Models\Client;
use Filament\Forms\Components\Select;

class ClientSelector extends Select
{
    protected string $view = 'filament.forms.components.client-selector';

    protected function setUp(): void
    {
        parent::setUp();

        $this->relationship('client', 'name')
            ->searchable(['name', 'surname', 'email', 'phone'])
            ->preload()
            ->live()
            ->getSearchResultsUsing(function (string $search) {
                return Client::query()
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->where('active', true)
                    ->limit(50)
                    ->get()
                    ->mapWithKeys(fn (Client $client) => [
                        $client->id => $this->formatClientLabel($client)
                    ]);
            })
            ->getOptionLabelFromRecordUsing(fn (Client $record) => $this->formatClientLabel($record))
            ->createOptionForm([
                \Filament\Forms\Components\Section::make('Client Information')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('surname')
                            ->maxLength(151),
                        \Filament\Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        \Filament\Forms\Components\Toggle::make('active')
                            ->default(true),
                    ])
                    ->columns(2),
            ])
            ->helperText('Search by name, email, or phone number');
    }

    protected function formatClientLabel(Client $client): string
    {
        $parts = array_filter([
            $client->computed_full_name ?? ($client->name . ($client->surname ? ' ' . $client->surname : '')),
            $client->email,
            $client->phone,
        ]);

        return implode(' • ', $parts);
    }

    public function activeOnly(bool $condition = true): static
    {
        if ($condition) {
            $this->modifyQueryUsing(fn ($query) => $query->where('active', true));
        }

        return $this;
    }
}
