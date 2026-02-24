<?php

namespace BlackpigCreatif\Sceau\SchemaGenerators;

use BlackpigCreatif\Sceau\Models\SeoData;

class HowToSchema extends BaseSchema
{
    public function getType(): string
    {
        return 'HowTo';
    }

    public function generate(SeoData $seoData): array
    {
        $schema = $this->baseSchema();

        $schema['name'] = $seoData->title ?? config('app.name');

        if ($description = $this->getDescription($seoData)) {
            $schema['description'] = $description;
        }

        if ($image = $this->getImage($seoData)) {
            $schema['image'] = $image;
        }

        return $this->removeNullValues($schema);
    }

    public function getSkeleton(): array
    {
        return [
            '@context'    => 'https://schema.org',
            '@type'       => 'HowTo',
            'name'        => '',
            'description' => '',
            'image'       => '',
            'totalTime'   => '',  // ISO 8601 duration, e.g. PT30M
            'estimatedCost' => [
                '@type'    => 'MonetaryAmount',
                'currency' => '',
                'value'    => '',
            ],
            'supply' => [
                [
                    '@type' => 'HowToSupply',
                    'name'  => '',
                ],
            ],
            'tool' => [
                [
                    '@type' => 'HowToTool',
                    'name'  => '',
                ],
            ],
            'step' => [
                [
                    '@type'    => 'HowToStep',
                    'name'     => '',
                    'text'     => '',
                    'image'    => '',
                    'url'      => '',
                ],
            ],
        ];
    }
}
