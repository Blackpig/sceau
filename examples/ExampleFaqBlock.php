<?php

/**
 * Example FAQ Block implementation for Atelier
 *
 * This shows how an FAQ block generates its own standalone FAQPage schema.
 * Copy this pattern to your Atelier FAQ block class.
 */

namespace App\Blocks;

use BlackpigCreatif\Atelier\Blocks\BaseBlock;
use BlackpigCreatif\Sceau\Concerns\InteractsWithSchema;

class FaqBlock extends BaseBlock
{
    use InteractsWithSchema;

    /**
     * This block generates its own standalone schema.
     */
    public function hasStandaloneSchema(): bool
    {
        return ! empty($this->data['pairs']);
    }

    /**
     * Generate FAQPage schema.
     */
    public function toStandaloneSchema(): ?array
    {
        if (empty($this->data['pairs'])) {
            return null;
        }

        $mainEntity = collect($this->data['pairs'])->map(function ($pair) {
            return [
                '@type' => 'Question',
                'name' => $pair['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $pair['answer'],
                ],
            ];
        })->toArray();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }
}
