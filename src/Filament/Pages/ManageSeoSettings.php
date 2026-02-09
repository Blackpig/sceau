<?php

namespace BlackpigCreatif\Sceau\Filament\Pages;

use BackedEnum;
use BlackpigCreatif\Sceau\Models\SeoSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageSeoSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'SEO Settings';

    protected static ?string $title = 'SEO Settings';

    protected string $view = 'sceau::pages.manage-seo-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 99;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getRecord()?->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Site Information')
                        ->description('Basic information about your website or business')
                        ->schema([
                            TextInput::make('site_name')
                                ->label('Site Name')
                                ->placeholder(fn (): string => config('app.name'))
                                ->helperText('Leave empty to use app name from config'),

                            TextInput::make('site_url')
                                ->label('Site URL')
                                ->url()
                                ->placeholder(fn (): string => config('app.url'))
                                ->helperText('Leave empty to use app URL from config'),
                        ])
                        ->columns(2),

                    Section::make('Contact Information')
                        ->description('Contact details for structured data')
                        ->schema([
                            TextInput::make('telephone')
                                ->label('Telephone')
                                ->tel()
                                ->placeholder('+1 (555) 123-4567'),

                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->placeholder('contact@example.com'),
                        ])
                        ->columns(2),

                    Section::make('Physical Address')
                        ->description('Address for LocalBusiness schema')
                        ->schema([
                            TextInput::make('address_street')
                                ->label('Street Address')
                                ->placeholder('123 Main Street'),

                            TextInput::make('address_city')
                                ->label('City')
                                ->placeholder('New York'),

                            TextInput::make('address_region')
                                ->label('State/Region')
                                ->placeholder('NY'),

                            TextInput::make('address_postal_code')
                                ->label('Postal Code')
                                ->placeholder('10001'),

                            TextInput::make('address_country')
                                ->label('Country Code')
                                ->placeholder('US')
                                ->maxLength(2)
                                ->helperText('Two-letter ISO country code (e.g., US, GB, FR)'),
                        ])
                        ->columns(3),

                    Section::make('Business Information')
                        ->description('Additional business data for LocalBusiness schema')
                        ->schema([
                            TextInput::make('price_range')
                                ->label('Price Range')
                                ->placeholder('$$')
                                ->helperText('Use $ symbols to indicate price range (e.g., $, $$, $$$)'),

                            Repeater::make('opening_hours')
                                ->label('Opening Hours')
                                ->schema([
                                    TextInput::make('dayOfWeek')
                                        ->label('Day(s) of Week')
                                        ->placeholder('Monday, Tuesday, Wednesday')
                                        ->helperText('Comma-separated or array notation'),

                                    TextInput::make('opens')
                                        ->label('Opens')
                                        ->placeholder('09:00')
                                        ->helperText('24-hour format (HH:MM)'),

                                    TextInput::make('closes')
                                        ->label('Closes')
                                        ->placeholder('17:00')
                                        ->helperText('24-hour format (HH:MM)'),
                                ])
                                ->addActionLabel('Add Hours')
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => ($state['dayOfWeek'] ?? null) ? "{$state['dayOfWeek']}: {$state['opens']} - {$state['closes']}" : 'New Hours')
                                ->helperText('Define when your business is open for LocalBusiness schema')
                                ->columnSpanFull(),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save Changes')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $record = $this->getRecord();

        if (! $record) {
            $record = new SeoSettings;
        }

        $record->fill($data);
        $record->save();

        if ($record->wasRecentlyCreated) {
            $this->form->record($record)->saveRelationships();
        }

        Notification::make()
            ->success()
            ->title('Saved')
            ->body('SEO settings have been saved successfully.')
            ->send();
    }

    public function getRecord(): ?SeoSettings
    {
        return SeoSettings::first();
    }
}
