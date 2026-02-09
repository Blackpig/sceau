<?php

namespace BlackpigCreatif\Sceau\Schemas\Runtime;

use Closure;
use Illuminate\Support\Collection;

class ProductListSchema
{
    /**
     * Generate an ItemList schema for products.
     */
    public static function make(Collection $products, ?Closure $transformer = null): array
    {
        $transformer ??= fn ($product, $index) => [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'item' => [
                '@type' => 'Product',
                'name' => $product->name,
                'url' => route('products.show', $product),
            ],
        ];

        $items = $products->map($transformer)->values()->toArray();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'numberOfItems' => $products->count(),
            'itemListElement' => $items,
        ];
    }

    /**
     * Generate a more detailed product ItemList with pricing and images.
     */
    public static function makeDetailed(Collection $products): array
    {
        return self::make($products, fn ($product, $index) => [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'item' => [
                '@type' => 'Product',
                'name' => $product->name,
                'url' => route('products.show', $product),
                'image' => $product->image_url ?? null,
                'description' => $product->description ?? null,
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $product->price,
                    'priceCurrency' => config('app.currency', 'EUR'),
                    'availability' => $product->in_stock
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                ],
            ],
        ]);
    }
}
