<?php

namespace BlackpigCreatif\Sceau\Filament\RelationManagers;

use BackedEnum;
use BlackpigCreatif\Sceau\Filament\Schemas\SeoDataForm;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoDataRelationManager extends RelationManager
{
    protected static string $relationship = 'seoData';

    protected static ?string $title = 'SEO';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedMagnifyingGlass;

    public function form(Schema $schema): Schema
    {
        return SeoDataForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Meta Title')
                    ->limit(50)
                    ->tooltip(fn ($record): string => $record->title ?? 'Not set'),

                TextColumn::make('description')
                    ->label('Meta Description')
                    ->limit(60)
                    ->tooltip(fn ($record): string => $record->description ?? 'Not set'),

                IconColumn::make('has_og')
                    ->label('OG')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => ! empty($record->open_graph)),

                IconColumn::make('has_schema')
                    ->label('Schema')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => $record->hasSchemaMarkup()),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add SEO Data')
                    ->icon(Heroicon::OutlinedPlus)
                    ->visible(fn (): bool => $this->getOwnerRecord()->seoData === null),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('No SEO data')
            ->emptyStateDescription('Add SEO metadata to optimize this content for search engines.')
            ->emptyStateIcon(Heroicon::OutlinedMagnifyingGlass);
    }
}
