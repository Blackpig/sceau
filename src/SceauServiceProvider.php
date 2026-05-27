<?php

namespace BlackpigCreatif\Sceau;

use BlackpigCreatif\Sceau\Services\SchemaStack;
use BlackpigCreatif\Sceau\View\Components\Head;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SceauServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        parent::boot();

        // Load migrations manually - this is more reliable
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('sceau')
            ->hasConfigFile()
            ->hasViews()
            /* ->hasMigrations([
                '2026_01_01_000001_create_seo_data_table',
                '2026_01_01_000002_create_seo_settings_table',
            ]) */;
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(SchemaStack::class);
    }

    public function packageBooted(): void
    {
        // Register the Sceau documentation Tome with Grimoire if it is installed.
        // Runs here (once per application boot) rather than in SceauPlugin::register()
        // which fires once per panel and would cause duplicate registration errors in
        // multi-panel applications.
        if (class_exists(\BlackpigCreatif\Grimoire\Facades\Grimoire::class)) {
            \BlackpigCreatif\Grimoire\Facades\Grimoire::registerTome(
                id: 'sceau',
                label: 'Sceau SEO',
                icon: 'heroicon-o-magnifying-glass',
                path: dirname(__DIR__) . '/resources/grimoire/sceau',
                clusterClass: \BlackpigCreatif\Sceau\Filament\Clusters\SceauDocumentationCluster::class,
                slug: 'sceau',
            );
        }

        // Register Blade component
        Blade::component('sceau-head', Head::class);

        // Register @seo directive
        Blade::directive('seo', function ($expression) {
            return "<?php echo app(\BlackpigCreatif\Sceau\View\Components\Head::class, ['model' => {$expression}])->render(); ?>";
        });
    }
}
