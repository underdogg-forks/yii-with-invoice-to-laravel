<?php

namespace App\Filament\Resources;

use App\Enums\TemplateCategoryEnum;
use App\Filament\Resources\TemplateResource\Pages;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Template Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Template Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('subject')
                            ->label('Email Subject')
                            ->maxLength(255)
                            ->helperText('Subject line for email templates')
                            ->columnSpan(1),
                        
                        Forms\Components\Select::make('category')
                            ->label('Template Category')
                            ->options(TemplateCategoryEnum::forSelect())
                            ->required()
                            ->default(TemplateCategoryEnum::CUSTOM->value)
                            ->columnSpan(1),
                        
                        Forms\Components\Toggle::make('is_default')
                            ->label('Default Template')
                            ->helperText('Use this as the default template for its category')
                            ->default(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Template Content')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('Template Content')
                            ->required()
                            ->fileAttachmentsDirectory('template-attachments')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                                'codeBlock',
                            ])
                            ->helperText('Available variables: {CLIENT_NAME}, {INVOICE_NUMBER}, {TOTAL_AMOUNT}')
                            ->columnSpan(2),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->helperText('Internal notes about this template')
                            ->columnSpan(2)
                            ->collapsed(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive templates cannot be used')
                            ->columnSpan(1),
                        
                        Forms\Components\DateTimePicker::make('created_at')
                            ->disabled()
                            ->columnSpan(1)
                            ->visibleOn('edit'),
                        
                        Forms\Components\DateTimePicker::make('updated_at')
                            ->disabled()
                            ->columnSpan(1)
                            ->visibleOn('edit'),
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
                    ->label('Template Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->colors([
                        'primary' => TemplateCategoryEnum::STANDARD->value,
                        'success' => TemplateCategoryEnum::CUSTOM->value,
                        'warning' => TemplateCategoryEnum::SYSTEM->value,
                        'danger' => TemplateCategoryEnum::ARCHIVED->value,
                    ])
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(TemplateCategoryEnum::forSelect())
                    ->multiple(),
                
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Default Templates')
                    ->nullable(),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Templates')
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Template Preview')
                    ->modalContent(fn (Template $record): \Illuminate\View\View => view(
                        'filament.resources.template.preview',
                        ['template' => $record]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Template $record) {
                        $newTemplate = $record->replicate();
                        $newTemplate->name = $record->name . ' (Copy)';
                        $newTemplate->is_default = false;
                        $newTemplate->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if (!$record->canBeDeleted()) {
                                    throw new \Exception("Cannot delete default template '{$record->name}'.");
                                }
                            }
                            $records->each->delete();
                        }),
                ]),
            ]);
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
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'view' => Pages\ViewTemplate::route('/{record}'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }
}
