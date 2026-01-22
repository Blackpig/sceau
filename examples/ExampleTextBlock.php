<?php

/**
 * Example Text Block implementation for Atelier
 *
 * This shows how a text block contributes to composite Article schema.
 * Copy this pattern to your Atelier BaseBlock or individual block classes.
 */

namespace App\Blocks;

use BlackpigCreatif\Atelier\Blocks\BaseBlock;
use BlackpigCreatif\Sceau\Concerns\InteractsWithSchema;

class TextBlock extends BaseBlock
{
    use InteractsWithSchema;

    /**
     * This block contributes text content to composite schemas (like Article).
     */
    public function contributesToComposite(): bool
    {
        return true;
    }

    /**
     * Provide the text content to be included in the article body.
     */
    public function getCompositeContribution(): array
    {
        return [
            'type' => 'text',
            'content' => $this->data['content'] ?? '',
        ];
    }
}
