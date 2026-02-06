<?php

namespace BlackpigCreatif\Sceau\Filament\Schemas;

use BlackpigCreatif\ChambreNoir\Conversions\SocialImageConversion;
use BlackpigCreatif\ChambreNoir\Forms\Components\RetouchMediaUpload;
use BlackpigCreatif\Sceau\Enums\OgType;
use BlackpigCreatif\Sceau\Enums\RobotsDirective;
use BlackpigCreatif\Sceau\Enums\SchemaType;
use BlackpigCreatif\Sceau\Enums\TwitterCardType;
use BlackpigCreatif\Sceau\Services\JsonLdGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SeoDataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->persistTabInQueryString('seo-tab')
                    ->tabs([
                        self::basicSeoTab(),
                        self::openGraphTab(),
                        self::twitterCardTab(),
                        self::schemaMarkupTab(),
                        self::aiOptimizationTab(),
                    ]),
            ]);
    }

    protected static function basicSeoTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Basic SEO')
            ->icon(Heroicon::OutlinedDocumentText)
            ->schema([
                Section::make('Meta Tags')
                    ->description('Essential meta tags for search engine optimization')
                    ->columnSpan('full')
                    ->columns(1)
                    ->schema([
                        TextInput::make('title')
                            ->label('Meta Title')
                            ->placeholder('Page title for search results')
                            ->maxLength(config('sceau.character_limits.meta_title', 70))
                            ->hint(fn (?string $state): string => (strlen($state ?? '').' / '.config('sceau.character_limits.meta_title', 70).' characters'))
                            ->hintColor(fn (?string $state): string => match (true) {
                                $state === null => 'gray',
                                strlen($state) >= 50 && strlen($state) <= 65 => 'success',
                                strlen($state) > config('sceau.character_limits.meta_title', 70) => 'danger',
                                default => 'warning',
                            })
                            ->helperText('Optimal: 50-65 characters. Maximum: '.config('sceau.character_limits.meta_title', 70).' characters.'),

                        Textarea::make('description')
                            ->label('Meta Description')
                            ->placeholder('Brief description for search results')
                            ->rows(3)
                            ->maxLength(config('sceau.character_limits.meta_description', 160))
                            ->hint(fn (?string $state): string => (strlen($state ?? '').' / '.config('sceau.character_limits.meta_description', 160).' characters'))
                            ->hintColor(fn (?string $state): string => match (true) {
                                $state === null => 'gray',
                                strlen($state) >= 150 && strlen($state) <= config('sceau.character_limits.meta_description', 160) => 'success',
                                strlen($state) > config('sceau.character_limits.meta_description', 160) => 'danger',
                                default => 'warning',
                            })
                            ->helperText('Optimal: 150-'.config('sceau.character_limits.meta_description', 160).' characters. Google may rewrite descriptions.'),

                        TextInput::make('focus_keyword')
                            ->label('Focus Keyword')
                            ->placeholder('Primary keyword to target')
                            ->maxLength(100)
                            ->helperText('Comma-separated keywords for this locale.'),

                        TextInput::make('canonical_url')
                            ->label('Canonical URL')
                            ->placeholder('https://example.com/page')
                            ->url()
                            ->helperText('Leave empty to use the page URL. Set to prevent duplicate content issues.'),

                        Select::make('robots_directive')
                            ->label('Robots Directive')
                            ->options(RobotsDirective::class)
                            ->default(RobotsDirective::IndexFollow)
                            ->helperText('Controls how search engines index and follow links on this page.'),
                    ]),
            ]);
    }

    protected static function openGraphTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Open Graph')
            ->icon(Heroicon::OutlinedShare)
            ->schema([
                Section::make('Social Sharing')
                    ->description('Controls how content appears when shared on Facebook, LinkedIn, etc.')
                    ->columnSpan('full')
                    ->columns(1)
                    ->schema([
                        TextInput::make('og_title')
                            ->label('OG Title')
                            ->placeholder('Leave empty to use Meta Title')
                            ->maxLength(config('sceau.character_limits.og_title', 95))
                            ->helperText('Optimal: 40-60 characters for Facebook.'),

                        Textarea::make('og_description')
                            ->label('OG Description')
                            ->placeholder('Leave empty to use Meta Description')
                            ->rows(2)
                            ->maxLength(config('sceau.character_limits.og_description', 200))
                            ->helperText('Optimal: 55-200 characters.'),

                        Toggle::make('og_use_hero_image')
                            ->label('Use Hero Image')
                            ->helperText('Use the hero block image from the page for social sharing.')
                            ->live()
                            ->default(false),

                        RetouchMediaUpload::make('og_image')
                            ->label('Social Image')
                            ->preset(SocialImageConversion::class)
                            ->maxFiles(1)
                            ->disk(fn (): string => config('sceau.uploads.disk', 'public'))
                            ->directory(fn (): string => config('sceau.uploads.directory', 'seo-images'))
                            ->helperText('Used for Facebook, Twitter, LinkedIn, etc. Optimal: 1200x630 pixels.')
                            ->visible(fn (Get $get): bool => ! $get('og_use_hero_image'))
                            ->columnSpanFull(),

                        Select::make('og_type')
                            ->label('OG Type')
                            ->options(OgType::class)
                            ->default(OgType::Website)
                            ->helperText('The type of content being shared.'),

                        TextInput::make('og_site_name')
                            ->label('Site Name')
                            ->placeholder(fn (): string => config('app.name'))
                            ->helperText('Your website or brand name.'),

                        TextInput::make('og_locale')
                            ->label('Locale')
                            ->placeholder('en_US')
                            ->maxLength(10)
                            ->helperText('Language and region (e.g., en_US, fr_FR).'),
                    ]),
            ]);
    }

    protected static function twitterCardTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Twitter Card')
            ->icon(Heroicon::OutlinedChatBubbleLeft)
            ->schema([
                Section::make('Twitter/X Sharing')
                    ->description('Controls how content appears when shared on Twitter/X')
                    ->columnSpan('full')
                    ->columns(1)
                    ->schema([
                        Select::make('twitter_card_type')
                            ->label('Card Type')
                            ->options(TwitterCardType::class)
                            ->default(TwitterCardType::SummaryLargeImage)
                            ->helperText('Summary Large Image is recommended for most content.'),

                        TextInput::make('twitter_title')
                            ->label('Twitter Title')
                            ->placeholder('Leave empty to use OG Title')
                            ->maxLength(config('sceau.character_limits.twitter_title', 70))
                            ->helperText('Maximum '.config('sceau.character_limits.twitter_title', 70).' characters.'),

                        Textarea::make('twitter_description')
                            ->label('Twitter Description')
                            ->placeholder('Leave empty to use OG Description')
                            ->rows(2)
                            ->maxLength(config('sceau.character_limits.twitter_description', 200))
                            ->helperText('Maximum '.config('sceau.character_limits.twitter_description', 200).' characters. Uses the Social Image from Open Graph tab.'),

                        TextInput::make('twitter_site')
                            ->label('Twitter Site')
                            ->placeholder('@yourcompany')
                            ->prefix('@')
                            ->maxLength(50)
                            ->helperText('Your company Twitter handle.'),

                        TextInput::make('twitter_creator')
                            ->label('Twitter Creator')
                            ->placeholder('@author')
                            ->prefix('@')
                            ->maxLength(50)
                            ->helperText('The content creator Twitter handle.'),
                    ]),
            ]);
    }

    protected static function schemaMarkupTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Schema Markup')
            ->icon(Heroicon::OutlinedCodeBracket)
            ->schema([
                Section::make('Structured Data (JSON-LD)')
                    ->description('Schema markup helps search engines understand your content')
                    ->columnSpan('full')
                    ->columns(1)
                    ->schema([
                        Select::make('schema_type')
                            ->label('Schema Type')
                            ->options(SchemaType::class)
                            ->live()
                            ->helperText('Select the type of content to generate appropriate structured data.'),

                        Textarea::make('schema_data')
                            ->label('Schema Data (JSON)')
                            ->rows(10)
                            ->helperText('Advanced: Enter custom JSON-LD data. Leave empty for auto-generation based on type.')
                            ->visible(fn (Get $get): bool => $get('schema_type') !== null)
                            ->hintAction(
                                Action::make('insertSchema')
                                    ->label('Insert Schema')
                                    ->icon(Heroicon::OutlinedDocumentPlus)
                                    ->requiresConfirmation(fn (Get $get): bool => ! empty($get('schema_data')))
                                    ->modalHeading('Insert Schema Template')
                                    ->modalDescription('Existing data will be replaced. Do you want to continue?')
                                    ->modalSubmitActionLabel('Insert')
                                    ->action(function (Set $set, Get $get) {
                                        $schemaType = $get('schema_type');

                                        if (! $schemaType) {
                                            return;
                                        }

                                        $generator = app(JsonLdGenerator::class)->getGenerator($schemaType);

                                        if (! $generator) {
                                            return;
                                        }

                                        $skeleton = $generator->getSkeleton();
                                        $json = json_encode($skeleton, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

                                        $set('schema_data', $json);
                                    })
                            ),
                    ]),
            ]);
    }

    protected static function aiOptimizationTab(): Tabs\Tab
    {
        return Tabs\Tab::make('AI Optimization')
            ->icon(Heroicon::OutlinedSparkles)
            ->schema([
                Section::make('Content Freshness')
                    ->description('Signals for AI search engines (ChatGPT, Perplexity, Google AI)')
                    ->columnSpan('full')
                    ->columns(1)
                    ->schema([
                        DateTimePicker::make('content_updated_at')
                            ->label('Content Last Updated')
                            ->native(false)
                            ->helperText('When was this content last meaningfully updated?'),

                        Textarea::make('update_notes')
                            ->label('Update Notes')
                            ->placeholder('What changed in the last update?')
                            ->rows(2)
                            ->helperText('Brief changelog for AI context.'),
                    ]),
            ]);
    }
}
