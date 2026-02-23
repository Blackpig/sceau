<?php

declare(strict_types=1);

namespace BlackpigCreatif\Sceau;

use BlackpigCreatif\Sceau\Filament\Clusters\SceauDocumentationCluster;
use Filament\Contracts\Plugin;
use Filament\Panel;

class SceauPlugin implements Plugin
{
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

    public function register(Panel $panel): void
    {
        $panel->pages([
            \BlackpigCreatif\Sceau\Filament\Pages\ManageSeoSettings::class,
        ]);

        // Register the Sceau user documentation Tome with Grimoire if it is installed.
        if (class_exists(\BlackpigCreatif\Grimoire\Facades\Grimoire::class)) {
            \BlackpigCreatif\Grimoire\Facades\Grimoire::registerTome(
                id: 'sceau',
                label: 'Sceau SEO',
                icon: 'heroicon-o-magnifying-glass',
                path: dirname(__DIR__) . '/resources/grimoire/sceau',
                clusterClass: SceauDocumentationCluster::class,
                slug: 'sceau',
            );

            // Discover the built-in Cluster and Chapter Page stubs from inside the package.
            $panel->discoverClusters(
                in: __DIR__ . '/Filament/Clusters',
                for: 'BlackpigCreatif\\Sceau\\Filament\\Clusters',
            );

            $panel->discoverPages(
                in: __DIR__ . '/Filament/Pages',
                for: 'BlackpigCreatif\\Sceau\\Filament\\Pages',
            );
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
