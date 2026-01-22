<?php

namespace BlackpigCreatif\Sceau\Filament\Schemas;

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
                        self::faqSchemaTab(),
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
                            ->maxLength(70)
                            ->hint(fn (?string $state): string => (strlen($state ?? '').' / 70 characters'))
                            ->hintColor(fn (?string $state): string => match (true) {
                                $state === null => 'gray',
                                strlen($state) >= 50 && strlen($state) <= 65 => 'success',
                                strlen($state) > 70 => 'danger',
                                default => 'warning',
                            })
                            ->helperText('Optimal: 50-65 characters. Maximum: 70 characters.'),

                        Textarea::make('description')
                            ->label('Meta Description')
                            ->placeholder('Brief description for search results')
                            ->rows(3)
                            ->maxLength(160)
                            ->hint(fn (?string $state): string => (strlen($state ?? '').' / 160 characters'))
                            ->hintColor(fn (?string $state): string => match (true) {
                                $state === null => 'gray',
                                strlen($state) >= 150 && strlen($state) <= 160 => 'success',
                                strlen($state) > 160 => 'danger',
                                default => 'warning',
                            })
                            ->helperText('Optimal: 150-160 characters. Google may rewrite descriptions.'),

                        TextInput::make('focus_keyword')
                            ->label('Focus Keyword')
                            ->placeholder('Primary keyword to target')
                            ->maxLength(100)
                            ->helperText('The main keyword this content should rank for.'),

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
                        TextInput::make('open_graph.title')
                            ->label('OG Title')
                            ->placeholder('Leave empty to use Meta Title')
                            ->maxLength(95)
                            ->helperText('Optimal: 40-60 characters for Facebook.'),

                        Textarea::make('open_graph.description')
                            ->label('OG Description')
                            ->placeholder('Leave empty to use Meta Description')
                            ->rows(2)
                            ->maxLength(200)
                            ->helperText('Optimal: 55-200 characters.'),

                        Toggle::make('og_use_hero_image')
                            ->label('Use Hero Image')
                            ->helperText('Use the hero block image from the page instead of uploading a custom image.')
                            ->live()
                            ->default(false),

                        RetouchMediaUpload::make('open_graph.image')
                            ->label('OG Image')
                            ->conversions([
                                'og' => [
                                    'width' => 1200,
                                    'height' => 630,
                                    'fit' => 'crop',
                                    'quality' => 90,
                                ],
                            ])
                            ->maxFiles(1)
                            ->disk(fn (): string => config('sceau.uploads.disk', 'public'))
                            ->directory(fn (): string => config('sceau.uploads.directory', 'seo-images'))
                            ->helperText('Recommended: 1200x630 pixels. This image appears in social shares.')
                            ->visible(fn (Get $get): bool => ! $get('og_use_hero_image'))
                            ->columnSpanFull(),

                        Select::make('open_graph.type')
                            ->label('OG Type')
                            ->options(OgType::class)
                            ->default(OgType::Website)
                            ->helperText('The type of content being shared.'),

                        TextInput::make('open_graph.site_name')
                            ->label('Site Name')
                            ->placeholder(fn (): string => config('app.name'))
                            ->helperText('Your website or brand name.'),

                        TextInput::make('open_graph.locale')
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
                        Select::make('twitter_card.card_type')
                            ->label('Card Type')
                            ->options(TwitterCardType::class)
                            ->default(TwitterCardType::SummaryLargeImage)
                            ->helperText('Summary Large Image is recommended for most content.'),

                        TextInput::make('twitter_card.title')
                            ->label('Twitter Title')
                            ->placeholder('Leave empty to use OG Title')
                            ->maxLength(70)
                            ->helperText('Maximum 70 characters.'),

                        Textarea::make('twitter_card.description')
                            ->label('Twitter Description')
                            ->placeholder('Leave empty to use OG Description')
                            ->rows(2)
                            ->maxLength(200)
                            ->helperText('Maximum 200 characters.'),

                        Toggle::make('twitter_use_hero_image')
                            ->label('Use Hero Image')
                            ->helperText('Use the hero block image from the page instead of uploading a custom image.')
                            ->live()
                            ->default(false),

                        RetouchMediaUpload::make('twitter_card.image')
                            ->label('Twitter Image')
                            ->conversions([
                                'twitter' => [
                                    'width' => 1200,
                                    'height' => 600,
                                    'fit' => 'crop',
                                    'quality' => 90,
                                ],
                            ])
                            ->maxFiles(1)
                            ->disk(fn (): string => config('sceau.uploads.disk', 'public'))
                            ->directory(fn (): string => config('sceau.uploads.directory', 'seo-images'))
                            ->helperText('Leave empty to use OG Image. Recommended: 1200x600 pixels.')
                            ->visible(fn (Get $get): bool => ! $get('twitter_use_hero_image'))
                            ->columnSpanFull(),

                        TextInput::make('twitter_card.site')
                            ->label('Twitter Site')
                            ->placeholder('@yourcompany')
                            ->prefix('@')
                            ->maxLength(50)
                            ->helperText('Your company Twitter handle.'),

                        TextInput::make('twitter_card.creator')
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

    protected static function faqSchemaTab(): Tabs\Tab
    {
        return Tabs\Tab::make('FAQ Schema')
            ->icon(Heroicon::OutlinedQuestionMarkCircle)
            ->schema([
                Section::make('FAQ Schema for Rich Snippets')
                    ->description('Generate FAQPage schema markup for Google Search rich snippets and voice search.')
                    ->columnSpan('full')
                    ->schema([
                        TextEntry::make('faq_warning')
                            ->label('')
                            ->state(new \Illuminate\Support\HtmlString('
                                <div class="rounded-lg bg-warning-50 dark:bg-warning-900/20 p-4 text-sm">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-warning-600 dark:text-warning-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <div class="space-y-2">
                                            <p class="font-semibold text-warning-800 dark:text-warning-200">
                                                ⚠️ Only add FAQs here if:
                                            </p>
                                            <ul class="list-disc list-inside space-y-1 text-warning-700 dark:text-warning-300 ml-2">
                                                <li><strong>No FAQ blocks exist on this page</strong> - Avoid duplicate FAQPage schemas</li>
                                                <li><strong>Questions are genuine</strong> - Answer real customer questions</li>
                                                <li><strong>Answers add value</strong> - Provide helpful, accurate information</li>
                                                <li><strong>Content is relevant</strong> - FAQs must relate to this specific page topic</li>
                                            </ul>
                                            <p class="text-warning-700 dark:text-warning-300 mt-2 italic">
                                                This feature is optional. Leave empty if you don\'t have relevant FAQs.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            '))
                            ->columnSpanFull(),

                        Repeater::make('faq_pairs')
                            ->label('FAQ Pairs')
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'New FAQ')
                            ->addActionLabel('Add FAQ')
                            ->helperText('These generate FAQPage schema markup that appears in Google Search rich snippets.')
                            ->schema([
                                TextInput::make('question')
                                    ->label('Question')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Is sterling silver hypoallergenic?'),

                                Textarea::make('answer')
                                    ->label('Answer')
                                    ->required()
                                    ->rows(3)
                                    ->placeholder('Provide a clear, helpful answer...'),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }
}
