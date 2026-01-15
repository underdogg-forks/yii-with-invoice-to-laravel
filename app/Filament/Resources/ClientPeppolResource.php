<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientPeppolResource\Pages;
use App\Models\ClientPeppol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientPeppolResource extends Resource
{
    protected static ?string $model = ClientPeppol::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Peppol';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'endpointid';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Peppol Identification')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('endpointid')
                            ->label('Endpoint ID')
                            ->maxLength(100)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('endpointid_schemeid')
                            ->label('Endpoint ID Scheme ID')
                            ->maxLength(4)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('identificationid')
                            ->label('Identification ID')
                            ->maxLength(100)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('identificationid_schemeid')
                            ->label('Identification ID Scheme ID')
                            ->maxLength(4)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Legal Entity')
                    ->schema([
                        Forms\Components\TextInput::make('legal_entity_registration_name')
                            ->label('Registration Name')
                            ->maxLength(100)
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('legal_entity_companyid')
                            ->label('Company ID')
                            ->maxLength(100)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('legal_entity_companyid_schemeid')
                            ->label('Company ID Scheme ID')
                            ->maxLength(5)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('legal_entity_company_legal_form')
                            ->label('Company Legal Form')
                            ->maxLength(50)
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Tax & Financial')
                    ->schema([
                        Forms\Components\TextInput::make('taxschemecompanyid')
                            ->label('Tax Scheme Company ID')
                            ->maxLength(100)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('taxschemeid')
                            ->label('Tax Scheme ID')
                            ->maxLength(7)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('financial_institution_branchid')
                            ->label('Financial Institution Branch ID')
                            ->maxLength(20)
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Additional')
                    ->schema([
                        Forms\Components\TextInput::make('accounting_cost')
                            ->label('Accounting Cost')
                            ->maxLength(30)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('buyer_reference')
                            ->label('Buyer Reference')
                            ->maxLength(20)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('supplier_assigned_accountid')
                            ->label('Supplier Assigned Account ID')
                            ->maxLength(20)
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('endpointid')
                    ->label('Endpoint ID')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('endpointid_schemeid')
                    ->label('Scheme ID')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('legal_entity_registration_name')
                    ->label('Registration Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('client')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientPeppols::route('/'),
            'create' => Pages\CreateClientPeppol::route('/create'),
            'edit' => Pages\EditClientPeppol::route('/{record}/edit'),
            'view' => Pages\ViewClientPeppol::route('/{record}'),
        ];
    }
}
