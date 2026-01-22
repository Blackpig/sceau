<?php

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
    }

    public function boot(Panel $panel): void
    {
        // No boot actions needed
    }
}
