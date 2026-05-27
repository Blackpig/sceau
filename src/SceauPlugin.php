<?php

declare(strict_types=1);

namespace BlackpigCreatif\Sceau;

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

        // Discover the built-in Cluster and Chapter Page stubs from inside the package.
        // Tome registration has moved to SceauServiceProvider::packageBooted() so it only
        // runs once per application boot rather than once per panel, preventing duplicate
        // registration errors in multi-panel applications.
        if (class_exists(\BlackpigCreatif\Grimoire\Facades\Grimoire::class)) {
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
