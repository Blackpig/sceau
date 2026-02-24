# Atelier Integration

Sceau integrates with [Atelier](https://github.com/blackpig-creatif/atelier) to generate Schema.org JSON-LD automatically from content blocks. When both packages are installed and the driver is configured, the `<x-sceau::head>` component handles everything — no controller boilerplate is required.

## Prerequisites

- Both `blackpig-creatif/atelier` and `blackpig-creatif/sceau` installed
- Your page model uses `HasAtelierBlocks` and `HasSeoData`
- `config/atelier.php` has `schema_driver` set to `SceauBlockSchemaDriver::class`

## Setup

### 1. Configure the driver

In your application's `config/atelier.php`:

```php
'schema_driver' => \BlackpigCreatif\Sceau\Schema\Drivers\SceauBlockSchemaDriver::class,
```

The `AtelierServiceProvider` reads this value on boot and registers `SceauBlockSchemaDriver` as the singleton bound to `BlockSchemaDriverInterface`. Setting the value to `null` disables driver-based schema generation entirely, with no side effects.

### 2. Use the Head component

```blade
<x-sceau::head :model="$page" />
```

That is all. The `Head` component detects that the model has `publishedBlocks` and calls `PageSchemaBuilder::build($page)` automatically before rendering the JSON-LD output.

---

## How PageSchemaBuilder Works

`PageSchemaBuilder::build(Model $page)` runs three sequential passes over `$page->publishedBlocks()->get()`. The collection is materialised once; all three passes work from the same result set.

### Pass 1 — Article schema

Checks whether any block implements `HasCompositeSchema` and returns `true` from `contributesToComposite()`. If at least one does, `ArticleSchema::fromBlocks()` assembles a single Article schema from all contributing blocks and pushes it to the `SchemaStack`.

The Article schema aggregates:
- `articleBody` — all `content` values (HTML stripped) joined with double newlines
- `image` — all `url` values (single image) and `urls` values (image arrays) flattened into one list
- `headline`, `description`, `author`, `publisher`, `datePublished`, `dateModified` — from `SeoData`

### Pass 2 — Legacy standalone schemas

For each block implementing `HasStandaloneSchema` where `hasStandaloneSchema()` returns `true`, calls `toStandaloneSchema()` and pushes the result to the `SchemaStack`. This pass exists for backwards compatibility; new blocks should use the driver pattern (Pass 3) instead.

### Pass 3 — Driver-based typed schemas

For each block implementing `HasSchemaContribution`, calls `getSchemaType()`. If non-null, passes the block to `SceauBlockSchemaDriver::resolveSchema()`, which matches on the type value and delegates to the appropriate schema generator. The resulting array is pushed to the `SchemaStack`.

---

## SceauBlockSchemaDriver

Namespace: `BlackpigCreatif\Sceau\Schema\Drivers\SceauBlockSchemaDriver`

Implements `BlockSchemaDriverInterface`. Receives a `HasSchemaContribution` block, reads its declared type, and delegates to Sceau's schema generators.

```php
public function resolveSchema(HasSchemaContribution $block): ?array
{
    $type = $block->getSchemaType();

    if ($type === null) {
        return null;
    }

    return match ($type->value) {
        SchemaType::FAQPage->value     => $this->buildFaqPageSchema($block->getSchemaData()),
        SchemaType::VideoObject->value => $this->buildVideoObjectSchema($block->getSchemaData()),
        default                        => null,
    };
}
```

The `match` operates on `$type->value` (the string backing value) rather than enum identity. This means blocks can declare their type using any backed enum — it does not have to be `BlackpigCreatif\Sceau\Enums\SchemaType` — as long as the string value matches a registered case.

### Supported types

| SchemaType | Data contract | Generator |
|------------|--------------|-----------|
| `FAQPage` | `['faqs' => [['question' => string, 'answer' => string], ...]]` | `FaqSchema::fromPairs()` |
| `VideoObject` | `['content_url' => string, 'embed_url' => ?string, 'name' => ?string, 'description' => ?string, 'thumbnail_url' => ?string]` | `VideoSchema::fromData()` |

### Adding a new type

1. Add a case to the `match` in `resolveSchema()`:
   ```php
   SchemaType::HowTo->value => $this->buildHowToSchema($block->getSchemaData()),
   ```

2. Add a `protected` builder method:
   ```php
   /**
    * @param array{name: string, steps: array<int, array{name: string, text: string}>} $data
    * @return array<string, mixed>|null
    */
   protected function buildHowToSchema(array $data): ?array
   {
       if (empty($data['steps'])) {
           return null;
       }

       return HowToSchema::fromData($data);
   }
   ```

3. Create `HowToSchema` (extend `BaseSchema`, add a `fromData(array $data): array` static method).

4. In the corresponding Atelier block, implement `getSchemaType()` and `getSchemaData()` with a matching data shape.

The `protected` visibility on builder methods means you can extend `SceauBlockSchemaDriver` in your application to override individual handlers without rewriting the whole driver.

---

## Schema Generators

### FaqSchema

`BlackpigCreatif\Sceau\SchemaGenerators\FaqSchema`

| Method | Description |
|--------|-------------|
| `generate(SeoData $seoData): array` | Generates FAQPage from `$seoData->faq_pairs`. Delegates to `fromPairs()`. |
| `fromPairs(array $pairs): array` | Generates FAQPage from a plain array of `[question, answer]` pairs. Used by the block driver. |

### VideoSchema

`BlackpigCreatif\Sceau\SchemaGenerators\VideoSchema`

| Method | Description |
|--------|-------------|
| `fromData(array $data): array` | Generates VideoObject from a data array. Accepts `content_url`, `embed_url`, `name`, `description`, `thumbnail_url`. |

---

## ArticleSchema

`BlackpigCreatif\Sceau\Schemas\Runtime\ArticleSchema`

Used by `PageSchemaBuilder` in Pass 1. Not called directly in typical usage.

```php
ArticleSchema::fromBlocks(Collection $blocks, ?SeoData $seoData = null): array
```

Iterates blocks, calls `getCompositeContribution()` on those implementing `HasCompositeSchema`, and assembles the Article schema. The `SeoData` argument fills `headline`, `description`, `author`, `publisher`, and date fields.

### Contribution shapes

The method understands two image shapes from block contributions:

```php
// Single image
['type' => 'text_with_image', 'content' => '...', 'url' => 'https://...']

// Multiple images
['type' => 'gallery', 'urls' => ['https://...', 'https://...']]
```

Both are collected and merged into a single flat image list.

---

## SchemaStack

`BlackpigCreatif\Sceau\Services\SchemaStack`

A request-scoped singleton. `PageSchemaBuilder` pushes schemas onto it; `Head` reads from it when rendering `<script type="application/ld+json">` tags.

```php
use BlackpigCreatif\Sceau\Facades\Schema;

// Push a schema manually (e.g. from a controller)
Schema::push([
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    // ...
]);
```

Schemas pushed via `PageSchemaBuilder` and schemas pushed manually from controllers are merged into the same stack. The `Head` component renders all of them.

---

## Dependency Direction

```
Atelier  defines  BlockSchemaDriverInterface
                  HasSchemaContribution
                  HasCompositeSchema
                  HasStandaloneSchema

Sceau    implements  BlockSchemaDriverInterface  (SceauBlockSchemaDriver)
         reads       HasSchemaContribution       (in PageSchemaBuilder)
         reads       HasCompositeSchema          (in ArticleSchema, PageSchemaBuilder)
         reads       HasStandaloneSchema         (in PageSchemaBuilder)
```

Atelier never imports from Sceau. Sceau imports Atelier's contracts. This means Atelier can be used without Sceau, and a different SEO package can implement its own driver against the same Atelier contracts.

---

## Disabling Integration

Set `schema_driver` to `null` in `config/atelier.php`. No `BlockSchemaDriverInterface` binding is registered, and Pass 3 of `PageSchemaBuilder` is silently skipped. Pass 1 (Article) and Pass 2 (standalone) continue to run if their respective contracts are implemented.

To disable all automatic schema generation:

```php
// config/atelier.php
'schema_driver' => null,
```

And remove or skip `PageSchemaBuilder::build()` calls (or avoid using `<x-sceau::head>` with models that have blocks).

---

## Testing

### Asserting schema output in feature tests

```php
use BlackpigCreatif\Sceau\Services\SchemaStack;

it('generates a faq page schema from a faqs block', function () {
    $page = Page::factory()->create();

    AtelierBlock::factory()->for($page, 'blockable')->create([
        'block_type'   => FaqsBlock::class,
        'is_published' => true,
    ]);

    // Seed attributes for the block...

    app()->forgetInstance(SchemaStack::class);

    $head = new \BlackpigCreatif\Sceau\View\Components\Head($page);

    $schema = collect($head->jsonLd)->firstWhere('@type', 'FAQPage');

    expect($schema)->not->toBeNull()
        ->and($schema['mainEntity'])->toHaveCount(2);
});
```

Reset the `SchemaStack` singleton between tests with `app()->forgetInstance(SchemaStack::class)` to prevent schema bleed across test cases.
