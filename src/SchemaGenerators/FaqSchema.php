<?php

namespace BlackpigCreatif\Sceau\SchemaGenerators;

use BlackpigCreatif\Sceau\Models\SeoData;

class FaqSchema extends BaseSchema
{
    public function getType(): string
    {
        return 'FAQPage';
    }

    public function generate(SeoData $seoData): array
    {
        $schema = $this->baseSchema();

        $mainEntity = [];

        foreach ($seoData->faq_pairs as $pair) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $pair['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $pair['answer'],
                ],
            ];
        }

        $schema['mainEntity'] = $mainEntity;

        return $schema;
    }

    public function getSkeleton(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => '',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => '',
                    ],
                ],
            ],
        ];
    }
}
