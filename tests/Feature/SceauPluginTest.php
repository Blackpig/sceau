<?php

use BlackpigCreatif\Sceau\Filament\Clusters\SceauDocumentationCluster;
use BlackpigCreatif\Sceau\Filament\Pages\ManageSeoSettings;
use BlackpigCreatif\Sceau\SceauPlugin;
use BlackpigCreatif\Sceau\Tests\Fixtures\CustomManageSeoSettings;
use Filament\Pages\Page;
use Filament\Panel;

function settingsPagesOn(Panel $panel): array
{
    return array_values(array_filter(
        $panel->getPages(),
        fn (string $page): bool => is_a($page, ManageSeoSettings::class, allow_string: true),
    ));
}

it('registers the built-in settings page by default', function () {
    $panel = Panel::make()->id('test')->plugin(SceauPlugin::make());

    expect(settingsPagesOn($panel))->toBe([ManageSeoSettings::class]);
});

it('registers only the replacement settings page when one is given', function () {
    $panel = Panel::make()->id('test')->plugin(
        SceauPlugin::make()->settingsPage(CustomManageSeoSettings::class),
    );

    expect(settingsPagesOn($panel))->toBe([CustomManageSeoSettings::class]);
});

it('rejects a settings page that does not extend the built-in page', function () {
    SceauPlugin::make()->settingsPage(Page::class);
})->throws(InvalidArgumentException::class);

it('registers the documentation cluster and every documentation page', function () {
    $panel = Panel::make()->id('test')->plugin(SceauPlugin::make());

    expect($panel->getClusters())->toContain(SceauDocumentationCluster::class)
        ->and($panel->getPages())->toContain(...SceauPlugin::DOCUMENTATION_PAGES);
});

it('lists every documentation page shipped in the package', function () {
    $shipped = collect(glob(dirname(__DIR__, 2).'/src/Filament/Pages/SceauDocumentation*Page.php'))
        ->map(fn (string $path): string => 'BlackpigCreatif\\Sceau\\Filament\\Pages\\'.basename($path, '.php'))
        ->sort()
        ->values()
        ->all();

    expect(collect(SceauPlugin::DOCUMENTATION_PAGES)->sort()->values()->all())->toBe($shipped);
});
