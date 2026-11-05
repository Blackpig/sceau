<?php

namespace BlackpigCreatif\Sceau\SchemaGenerators;

use BlackpigCreatif\Sceau\Models\SeoData;

class FaqSchema extends BaseSchema
{
    public function getType(): string
    {
        return 'FAQPage';
    }

    /**
     * SeoData holds no Q&A pairs of its own: FAQPage schemas are built from Atelier
     * FAQ blocks via the SchemaStack, or from custom schema_data JSON. Returning an
     * empty array tells JsonLdGenerator to emit nothing rather than an empty FAQPage.
     */
    public function generate(SeoData $seoData): array
    {
        return [];
    }

    /**
     * Build a FAQPage schema array directly from Q&A pairs,
     * without requiring a SeoData model instance.
     *
     * @param  array<int, array{question: string, answer: string}>  $pairs
     * @return array<string, mixed>
     */
    public static function fromPairs(array $pairs): array
    {
        $instance = new static;
        $schema = $instance->baseSchema();

        $schema['mainEntity'] = array_map(
            fn (array $pair): array => [
                '@type' => 'Question',
                'name' => $pair['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $pair['answer'],
                ],
            ],
            $pairs
        );

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
