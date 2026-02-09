# Sceau

**SEO metadata management plugin for FilamentPHP v4**

Sceau provides comprehensive SEO metadata management for Laravel applications using Filament v4. It offers a polymorphic approach to SEO data, allowing any Eloquent model to have associated meta tags, Open Graph data, Twitter Cards, and JSON-LD structured data.

## Features

- **Polymorphic SEO Data** - Attach SEO metadata to any Eloquent model
- **Filament Integration** - Manage SEO through a clean RelationManager interface
- **Meta Tags** - Title, description, canonical URLs, robots directives
- **Social Media** - Open Graph and Twitter Card support
- **Structured Data** - Extensible JSON-LD schema generators for Schema.org markup
- **Settings Management** - Database-backed global SEO settings via Filament panel
- **Image Processing** - Integrated with Chambre Noir for optimized OG/Twitter images
- **AI Optimization** - Content freshness signals and FAQ schema for modern search engines
- **Blade Component** - Simple `@seo($model)` directive for frontend rendering

## Requirements

- PHP 8.2+
- Laravel 11.0+ or 12.0+
- Filament 4.0+

## Installation

```bash
composer require blackpig-creatif/sceau
```

Run the migration:

```bash
php artisan migrate
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag=sceau-config
```

## Basic Usage

### 1. Add the Trait to Your Model

```php
use BlackpigCreatif\Sceau\Concerns\HasSeoData;

class Page extends Model
{
    use HasSeoData;
}
```

### 2. Add the RelationManager to Your Filament Resource

```php
use BlackpigCreatif\Sceau\Filament\RelationManagers\SeoDataRelationManager;

class PageResource extends Resource
{
    public static function getRelations(): array
    {
        return [
            SeoDataRelationManager::class,
        ];
    }
}
```

### 3. Render SEO Tags in Your Layout

```blade
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @seo($page)
</head>
```

Or use the component directly:

```blade
<x-sceau::head :model="$page" />
```

## Configuration

The config file (`config/sceau.php`) allows you to customize:

- **Upload disk and directory** for OG/Twitter images
- **Character limits** for titles and descriptions
- **Default values** for robots directives, OG types, Twitter card types
- **Available schema types** (filter which types appear in the select)

### Global SEO Settings

Sceau includes a settings page accessible from your Filament panel at **Settings > SEO Settings**. Configure:

- Site name and URL
- Contact information (phone, email)
- Business address
- Price range and opening hours (for LocalBusiness schema)

To add the settings page to your panel, register the plugin:

```php
use BlackpigCreatif\Sceau\SceauPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            SceauPlugin::make(),
        ]);
}
```

## Schema Architecture

Sceau uses a **dual-layer approach** to schema generation:

1. **Model-Level Schemas** (SeoData) - For entity-specific metadata (Organization, LocalBusiness, Product models)
2. **Runtime Schemas** (SchemaStack) - For page-specific content assembled at request time

This separation aligns with how modern web applications work: pages are built dynamically from blocks, products are fetched by controllers, and breadcrumbs depend on the current route.

### Model-Level Schemas (SeoData)

These schemas describe entities that exist as models in your database:

- **Organization** - Company/organization data from settings
- **LocalBusiness** - Business with address/hours from settings
- **Product** - For dedicated Product models with SEO data
- **Person** - For author/profile models

Configure these through the Filament RelationManager on your models.

### Runtime Schemas (SchemaStack)

These schemas describe content assembled at runtime:

- **Article/BlogPosting** - Built from Atelier blocks
- **ItemList** - Product listings, search results
- **BreadcrumbList** - Navigation breadcrumbs
- **FAQPage** - FAQ blocks on pages
- **VideoObject** - Video blocks

## Runtime Schema Generation

### The Schema Facade

Push schemas onto the stack during request processing:

```php
use BlackpigCreatif\Sceau\Facades\Schema;

public function show(Page $page)
{
    $products = Product::where('category_id', $page->category_id)->paginate(24);

    // Push runtime schemas
    Schema::push([
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'numberOfItems' => $products->total(),
        'itemListElement' => $products->map(fn($product, $index) => [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'item' => [
                '@type' => 'Product',
                'name' => $product->name,
                'url' => route('products.show', $product),
            ],
        ])->values()->toArray(),
    ]);

    return view('pages.show', compact('page', 'products'));
}
```

### Runtime Schema Helpers

Use helper classes for common patterns:

```php
use BlackpigCreatif\Sceau\Facades\Schema;
use BlackpigCreatif\Sceau\Schemas\Runtime\ProductListSchema;
use BlackpigCreatif\Sceau\Schemas\Runtime\BreadcrumbListSchema;

public function show(Page $page)
{
    $products = Product::paginate(24);

    // Product list with pricing
    Schema::push(ProductListSchema::makeDetailed($products));

    // Breadcrumbs
    Schema::push(BreadcrumbListSchema::make([
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Products', 'url' => route('products.index')],
        ['name' => $page->category->name, 'url' => route('category', $page->category)],
    ]));

    return view('pages.show', compact('page', 'products'));
}
```

**Available Helpers:**
- `ArticleSchema::fromBlocks($blocks, $seoData)` - Generate Article from Atelier blocks
- `ProductListSchema::make($products, $transformer)` - Generate ItemList
- `ProductListSchema::makeDetailed($products)` - ItemList with pricing/images
- `BreadcrumbListSchema::make($breadcrumbs)` - Generate BreadcrumbList

### Block-Based Schema Generation

For pages built with **Atelier blocks**, use the `PageSchemaBuilder`:

```php
use BlackpigCreatif\Sceau\Services\PageSchemaBuilder;

public function show(Page $page)
{
    // Automatically generates schemas from all page blocks
    PageSchemaBuilder::build($page);

    return view('pages.show', ['page' => $page]);
}
```

This will:
1. Generate an **Article schema** from text/image blocks
2. Generate **standalone schemas** from FAQ, Video, and other special blocks
3. Respect block configuration (published status, etc.)

### Atelier Block Integration

Add schema contribution to your Atelier blocks using the `InteractsWithSchema` trait:

**Text Block (contributes to Article):**
```php
use BlackpigCreatif\Atelier\Blocks\BaseBlock;
use BlackpigCreatif\Sceau\Concerns\InteractsWithSchema;

class TextBlock extends BaseBlock
{
    use InteractsWithSchema;

    public function contributesToComposite(): bool
    {
        return true;
    }

    public function getCompositeContribution(): array
    {
        return [
            'type' => 'text',
            'content' => $this->data['content'] ?? '',
        ];
    }
}
```

**FAQ Block (generates standalone schema):**
```php
use BlackpigCreatif\Atelier\Blocks\BaseBlock;
use BlackpigCreatif\Sceau\Concerns\InteractsWithSchema;

class FaqBlock extends BaseBlock
{
    use InteractsWithSchema;

    public function hasStandaloneSchema(): bool
    {
        return !empty($this->data['pairs']);
    }

    public function toStandaloneSchema(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($this->data['pairs'])->map(fn($pair) => [
                '@type' => 'Question',
                'name' => $pair['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $pair['answer'],
                ],
            ])->toArray(),
        ];
    }
}
```

See `/examples` directory for more block implementations.

### Multiple Schemas Per Page

Schema.org supports (and Google expects) multiple independent schemas on a single page:

```php
public function show(Page $page)
{
    // Article content from blocks
    PageSchemaBuilder::build($page);

    // Product carousel at bottom
    Schema::push(ProductListSchema::make($featuredProducts));

    // Category FAQs
    Schema::push([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [...],
    ]);

    // Breadcrumbs
    Schema::push(BreadcrumbListSchema::make($breadcrumbs));
}
```

Final output will be an **array of schemas**, each validated independently by Google.

## Extending SeoData Schemas

### Overriding Methods in a Subclass

Create a local schema class that extends one of the base generators:

```php
namespace App\SEO\Schemas;

use BlackpigCreatif\Sceau\SchemaGenerators\ArticleSchema;
use BlackpigCreatif\Sceau\Models\SeoData;

class CustomArticleSchema extends ArticleSchema
{
    protected function getAuthor(SeoData $seoData): array|null
    {
        $page = $seoData->seoable;

        // Custom logic for multi-author support
        if ($page->authors && $page->authors->count() > 0) {
            return $page->authors->map(fn($author) => [
                '@type' => 'Person',
                'name' => $author->name,
                'url' => route('authors.show', $author),
            ])->toArray();
        }

        return parent::getAuthor($seoData);
    }

    protected function getPublisher(SeoData $seoData): array|null
    {
        return [
            '@type' => 'Organization',
            'name' => config('app.name'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/logo.png'),
            ],
            'url' => config('app.url'),
        ];
    }
}
```

Register your custom schema in `AppServiceProvider`:

```php
use BlackpigCreatif\Sceau\Services\JsonLdGenerator;
use BlackpigCreatif\Sceau\Enums\SchemaType;
use App\SEO\Schemas\CustomArticleSchema;

public function boot(): void
{
    $generator = app(JsonLdGenerator::class);
    $generator->registerGenerator(
        SchemaType::Article,
        new CustomArticleSchema
    );
}
```

### Creating Custom Schema Types

For schema types not included in the package:

```php
namespace App\SEO\Schemas;

use BlackpigCreatif\Sceau\SchemaGenerators\BaseSchema;
use BlackpigCreatif\Sceau\Models\SeoData;

class CourseSchema extends BaseSchema
{
    public function getType(): string
    {
        return 'Course';
    }

    public function generate(SeoData $seoData): array
    {
        $schema = $this->baseSchema();

        $course = $seoData->seoable;

        $schema['name'] = $this->getName($seoData);
        $schema['description'] = $this->getDescription($seoData);
        $schema['provider'] = [
            '@type' => 'Organization',
            'name' => config('app.name'),
        ];

        if ($course->price) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $course->price,
                'priceCurrency' => 'USD',
            ];
        }

        return $this->removeNullValues($schema);
    }
}
```

Register it:

```php
use App\SEO\Schemas\CourseSchema;

$generator->registerGenerator(
    SchemaType::Course, // Add this to SchemaType enum first
    new CourseSchema
);
```

### Container Binding for Global Override

To replace a schema generator globally without touching the package:

```php
use BlackpigCreatif\Sceau\SchemaGenerators\ArticleSchema;
use App\SEO\Schemas\CustomArticleSchema;

public function register(): void
{
    $this->app->bind(ArticleSchema::class, CustomArticleSchema::class);
}
```

Now whenever `JsonLdGenerator` instantiates `new ArticleSchema`, Laravel's container will resolve your custom implementation instead.

## Accessing SEO Data

The `HasSeoData` trait provides convenient accessor methods:

```php
$page = Page::with('seoData')->first();

// Relationship
$seoData = $page->seoData;

// Helper methods
$title = $page->getSeoTitle(); // Falls back to $page->title or $page->name
$description = $page->getSeoDescription();
$hasSchema = $page->seoData?->hasSchemaMarkup();
$hasFaq = $page->seoData?->hasFaqPairs();

// Specific attribute with fallback
$ogTitle = $page->getSeoAttribute('open_graph.title', 'Default Title');
```

## Advanced: Image Resolution

Sceau integrates with **Chambre Noir** for image processing and supports **Atelier** blocks for hero image resolution.

When rendering OG/Twitter images, the package will:
1. Check for explicitly uploaded images in SEO data
2. Fall back to the first published Atelier hero block's background image
3. Apply the appropriate Chambre Noir conversion (`og`, `twitter`, etc.)

Override this behavior by extending `SeoData` and customizing the image resolution methods.

## License

MIT License. See [LICENSE](LICENSE) for details.
