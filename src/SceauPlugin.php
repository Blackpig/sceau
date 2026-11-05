<?php

declare(strict_types=1);

namespace BlackpigCreatif\Sceau;

use BlackpigCreatif\Grimoire\Facades\Grimoire;
use BlackpigCreatif\Sceau\Filament\Pages\ManageSeoSettings;
use BlackpigCreatif\Sceau\Filament\Pages\SceauDocumentationAiOptimizationPage;
use BlackpigCreatif\Sceau\Filament\Pages\SceauDocumentationBasicSeoPage;
use BlackpigCreatif\Sceau\Filament\Pages\SceauDocumentationIndexPage;
use BlackpigCreatif\Sceau\Filament\Pages\SceauDocumentationOpenGraphPage;
use BlackpigCreatif\Sceau\Filament\Pages\SceauDocumentationSchemaMarkupPage;
use BlackpigCreatif\Sceau\Filament\Pages\SceauDocumentationSeoSettingsPage;
use BlackpigCreatif\Sceau\Filament\Pages\SceauDocumentationTwitterCardPage;
use Filament\Contracts\Plugin;
use Filament\Panel;
use InvalidArgumentException;

class SceauPlugin implements Plugin
{
    public const array DOCUMENTATION_PAGES = [
        SceauDocumentationIndexPage::class,
        SceauDocumentationBasicSeoPage::class,
        SceauDocumentationOpenGraphPage::class,
        SceauDocumentationTwitterCardPage::class,
        SceauDocumentationSchemaMarkupPage::class,
        SceauDocumentationSeoSettingsPage::class,
        SceauDocumentationAiOptimizationPage::class,
    ];

    /** @var class-string<ManageSeoSettings> */
    protected string $settingsPage = ManageSeoSettings::class;

    public function getId(): string
    {
        return 'sceau';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    /**
     * Replace the built-in SEO settings page with a subclass, e.g. one that
     * scopes settings per panel. Only the given class is registered.
     *
     * @param  class-string<ManageSeoSettings>  $page
     */
    public function settingsPage(string $page): static
    {
        if (! is_a($page, ManageSeoSettings::class, allow_string: true)) {
            throw new InvalidArgumentException("[{$page}] must extend ".ManageSeoSettings::class.'.');
        }

        $this->settingsPage = $page;

        return $this;
    }

    /**
     * @return class-string<ManageSeoSettings>
     */
    public function getSettingsPage(): string
    {
        return $this->settingsPage;
    }

    public function register(Panel $panel): void
    {
        $panel->pages([$this->settingsPage]);

        if (! class_exists(Grimoire::class)) {
            return;
        }

        $panel->discoverClusters(
            in: __DIR__.'/Filament/Clusters',
            for: 'BlackpigCreatif\\Sceau\\Filament\\Clusters',
        );

        // Listed rather than discovered: the pages directory also holds ManageSeoSettings,
        // and discovering it would register the base page alongside any replacement.
        $panel->pages(self::DOCUMENTATION_PAGES);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
