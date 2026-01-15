<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Clients';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Client Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('surname')
                            ->maxLength(151)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('mobile')
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(1),
                        
                        Forms\Components\Toggle::make('active')
                            ->default(true)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\TextInput::make('address_1')
                            ->maxLength(100)
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('address_2')
                            ->maxLength(100)
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('zip')
                            ->maxLength(10)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('country')
                            ->maxLength(30)
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Tax Information')
                    ->schema([
                        Forms\Components\TextInput::make('vat_id')
                            ->label('VAT ID')
                            ->maxLength(30)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('tax_code')
                            ->maxLength(20)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('active')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('active', true))
                    ->toggle()
                    ->default(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('peppol')
                    ->label('Peppol Config')
                    ->icon('heroicon-o-cog')
                    ->url(fn (Client $record): string => route('clients.peppol', $record))
                    ->visible(fn (Client $record): bool => $record->active),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
            'view' => Pages\ViewClient::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('active', true)->count();
    }
}
