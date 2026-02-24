<?php

namespace BlackpigCreatif\Sceau\Schema\Drivers;

use BlackpigCreatif\Atelier\Contracts\BlockSchemaDriverInterface;
use BlackpigCreatif\Atelier\Contracts\HasSchemaContribution;
use BlackpigCreatif\Sceau\Enums\SchemaType;
use BlackpigCreatif\Sceau\SchemaGenerators\FaqSchema;
use BlackpigCreatif\Sceau\SchemaGenerators\VideoSchema;
use BlackpigCreatif\Sceau\Schemas\Runtime\ProductListSchema;
use Illuminate\Support\Collection;

class SceauBlockSchemaDriver implements BlockSchemaDriverInterface
{
    /**
     * Resolve a schema.org structured data array for the given block.
     * Returns null when the block declares no typed schema or the type is not yet supported.
     *
     * To add a new SchemaType:
     *   1. Add a case to the match expression below
     *   2. Add a protected buildXxxSchema(array $data): ?array method
     *   3. Add a fromXxx() static method to the corresponding SchemaGenerator
     */
    public function resolveSchema(HasSchemaContribution $block): ?array
    {
        $type = $block->getSchemaType();

        if ($type === null) {
            return null;
        }

        return match ($type->value) {
            SchemaType::FAQPage->value => $this->buildFaqPageSchema($block->getSchemaData()),
            SchemaType::ItemList->value => $this->buildItemListSchema($block->getSchemaData()),
            SchemaType::VideoObject->value => $this->buildVideoObjectSchema($block->getSchemaData()),
            default => null,
        };
    }

    /**
     * @param  array{faqs: array<int, array{question: string, answer: string}>}  $data
     * @return array<string, mixed>|null
     */
    protected function buildFaqPageSchema(array $data): ?array
    {
        $pairs = $data['faqs'] ?? [];

        if (empty($pairs)) {
            return null;
        }

        return FaqSchema::fromPairs($pairs);
    }

    /**
     * @param  array{content_url: string, embed_url?: string|null, name?: string|null, description?: string|null, thumbnail_url?: string|null}  $data
     * @return array<string, mixed>|null
     */
    protected function buildVideoObjectSchema(array $data): ?array
    {
        if (empty($data['content_url'])) {
            return null;
        }

        return VideoSchema::fromData($data);
    }

    /**
     * @param  array{products: array<int, array{name: string, description: string, price: ?string, image: ?string, available: bool}>}  $data
     * @return array<string, mixed>|null
     */
    protected function buildItemListSchema(array $data): ?array
    {
        $items = $data['products'] ?? [];

        if (empty($items)) {
            return null;
        }

        $collection = Collection::make($items);

        return ProductListSchema::make($collection, function (array $product, int $index): array {
            $item = [
                '@type' => 'Product',
                'name' => $product['name'],
                'description' => $product['description'],
            ];

            if (! empty($product['image'])) {
                $item['image'] = $product['image'];
            }

            if (! empty($product['price'])) {
                $item['offers'] = [
                    '@type' => 'Offer',
                    'price' => $product['price'],
                    'priceCurrency' => config('app.currency', 'EUR'),
                    'availability' => $product['available']
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                ];
            }

            return [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => $item,
            ];
        });
    }
}
