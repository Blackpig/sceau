<?php

namespace BlackpigCreatif\Sceau\SchemaGenerators;

use BlackpigCreatif\Sceau\Models\SeoData;

class ProductSchema extends BaseSchema
{
    public function getType(): string
    {
        return 'Product';
    }

    public function generate(SeoData $seoData): array
    {
        $schema = $this->baseSchema();

        if ($seoData->title) {
            $schema['name'] = $seoData->title;
        }

        if ($description = $this->getDescription($seoData)) {
            $schema['description'] = $description;
        }

        if ($image = $this->getImage($seoData)) {
            $schema['image'] = $image;
        }

        // Remove null values
        return $this->removeNullValues($schema);
    }

    public function getSkeleton(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => '',
            'description' => '',
            'image' => '',
            'offers' => [
                '@type' => 'Offer',
                'price' => '',
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock',
            ],
        ];
    }
}
