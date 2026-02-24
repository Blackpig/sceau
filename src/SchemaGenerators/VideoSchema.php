<?php

namespace BlackpigCreatif\Sceau\SchemaGenerators;

use BlackpigCreatif\Sceau\Models\SeoData;

class VideoSchema extends BaseSchema
{
    public function getType(): string
    {
        return 'VideoObject';
    }

    public function generate(SeoData $seoData): array
    {
        return static::fromData([]);
    }

    /**
     * Build a VideoObject schema array directly from block data,
     * without requiring a SeoData model instance.
     *
     * Expected $data shape:
     * [
     *   'content_url'  => string,        // required — direct URL to the video file or page
     *   'embed_url'    => string|null,   // optional — embeddable URL (YouTube/Vimeo iframe src)
     *   'name'         => string|null,   // optional — video title
     *   'description'  => string|null,   // optional — video description
     *   'thumbnail_url'=> string|null,   // optional — URL of a thumbnail image
     * ]
     *
     * @param  array{content_url: string, embed_url?: string|null, name?: string|null, description?: string|null, thumbnail_url?: string|null}  $data
     * @return array<string, mixed>
     */
    public static function fromData(array $data): array
    {
        $instance = new static;
        $schema = $instance->baseSchema();

        $schema['contentUrl'] = $data['content_url'] ?? '';

        if (! empty($data['embed_url'])) {
            $schema['embedUrl'] = $data['embed_url'];
        }

        if (! empty($data['name'])) {
            $schema['name'] = $data['name'];
        }

        if (! empty($data['description'])) {
            $schema['description'] = $data['description'];
        }

        if (! empty($data['thumbnail_url'])) {
            $schema['thumbnailUrl'] = $data['thumbnail_url'];
        }

        return $instance->removeNullValues($schema);
    }
}
