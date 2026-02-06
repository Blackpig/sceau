<?php

namespace BlackpigCreatif\Sceau;

use BlackpigCreatif\Sceau\Services\SchemaStack;
use BlackpigCreatif\Sceau\View\Components\Head;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SceauServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('sceau')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations([
                '2026_02_06_000001_create_seo_data_table',
                '2026_02_06_000002_create_seo_settings_table',
            ]);
    }

    public function packageRegistered(): void
    {
        // Register SchemaStack as a singleton
        $this->app->singleton(SchemaStack::class);
    }

    public function packageBooted(): void
    {
        // Register Blade component
        Blade::component('sceau-head', Head::class);

        // Register @seo directive
        Blade::directive('seo', function ($expression) {
            return "<?php echo app(\BlackpigCreatif\Sceau\View\Components\Head::class, ['model' => {$expression}])->render(); ?>";
        });
    }
}
