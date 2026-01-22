<?php

namespace BlackpigCreatif\Sceau\Schemas\Runtime;

use BlackpigCreatif\Sceau\Models\SeoData;
use Illuminate\Support\Collection;

class ArticleSchema
{
    /**
     * Generate an Article schema from blocks and SEO data.
     */
    public static function fromBlocks(Collection $blocks, ?SeoData $seoData = null): array
    {
        // Collect all composite contributions from blocks
        $contributions = $blocks
            ->map(fn ($block) => $block->hydrateBlock())
            ->filter(fn ($instance) => method_exists($instance, 'contributesToComposite')
                && $instance->contributesToComposite())
            ->map(fn ($instance) => $instance->getCompositeContribution())
            ->filter();

        // Build article body from text contributions
        $articleBody = $contributions
            ->where('type', 'text')
            ->pluck('content')
            ->filter()
            ->implode("\n\n");

        // Collect images
        $images = $contributions
            ->where('type', 'image')
            ->pluck('url')
            ->filter()
            ->values()
            ->toArray();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
        ];

        // Headline from SEO data or parent model
        if ($seoData?->title) {
            $schema['headline'] = $seoData->title;
        } elseif ($seoData?->seoable?->title) {
            $schema['headline'] = $seoData->seoable->title;
        }

        // Description
        if ($seoData?->description) {
            $schema['description'] = $seoData->description;
        }

        // Article body from blocks
        if ($articleBody) {
            $schema['articleBody'] = $articleBody;
        }

        // Images
        if (! empty($images)) {
            $schema['image'] = count($images) === 1 ? $images[0] : $images;
        } elseif ($seoData) {
            // Fallback to OG image
            if ($ogImage = $seoData->getOgImage()) {
                $schema['image'] = $ogImage;
            }
        }

        // Author
        if ($seoData?->seoable && method_exists($seoData->seoable, 'author') && $seoData->seoable->author) {
            $schema['author'] = [
                '@type' => 'Person',
                'name' => $seoData->seoable->author->name,
            ];
        }

        // Publisher from settings
        $settings = \BlackpigCreatif\Sceau\Models\SeoSettings::first();
        if ($settings?->site_name) {
            $schema['publisher'] = [
                '@type' => 'Organization',
                'name' => $settings->site_name,
            ];

            if ($settings->site_url) {
                $schema['publisher']['url'] = $settings->site_url;
            }
        }

        // Dates
        if ($seoData?->seoable?->created_at) {
            $schema['datePublished'] = $seoData->seoable->created_at->toIso8601String();
        }

        if ($seoData?->content_updated_at) {
            $schema['dateModified'] = $seoData->content_updated_at->toIso8601String();
        } elseif ($seoData?->seoable?->updated_at) {
            $schema['dateModified'] = $seoData->seoable->updated_at->toIso8601String();
        }

        // Remove null values
        return self::removeNullValues($schema);
    }

    /**
     * Remove null values from array recursively.
     */
    protected static function removeNullValues(array $array): array
    {
        return array_filter($array, function ($value) {
            if (is_array($value)) {
                return ! empty(self::removeNullValues($value));
            }

            return $value !== null;
        });
    }
}
