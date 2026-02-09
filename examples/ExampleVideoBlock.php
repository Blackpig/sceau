<?php

/**
 * Example Video Block implementation for Atelier
 *
 * This shows how a video block generates its own standalone VideoObject schema.
 * Copy this pattern to your Atelier video block class.
 */

namespace App\Blocks;

use BlackpigCreatif\Atelier\Blocks\BaseBlock;
use BlackpigCreatif\Sceau\Concerns\InteractsWithSchema;

class VideoBlock extends BaseBlock
{
    use InteractsWithSchema;

    /**
     * This block generates its own standalone schema.
     */
    public function hasStandaloneSchema(): bool
    {
        return ! empty($this->data['video_url']);
    }

    /**
     * Generate VideoObject schema.
     */
    public function toStandaloneSchema(): ?array
    {
        if (empty($this->data['video_url'])) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => $this->data['title'] ?? 'Video',
            'description' => $this->data['description'] ?? null,
            'thumbnailUrl' => $this->data['thumbnail_url'] ?? null,
            'uploadDate' => $this->created_at?->toIso8601String(),
            'contentUrl' => $this->data['video_url'],
            'embedUrl' => $this->data['embed_url'] ?? null,
            'duration' => $this->data['duration'] ?? null, // ISO 8601 format: PT1M30S
        ];
    }
}
