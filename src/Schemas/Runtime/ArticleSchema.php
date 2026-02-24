<?php

namespace BlackpigCreatif\Sceau\Schemas\Runtime;

use BlackpigCreatif\Atelier\Contracts\HasCompositeSchema;
use BlackpigCreatif\Sceau\Enums\SchemaType;
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
            ->filter(fn ($instance) => $instance instanceof HasCompositeSchema
                && $instance->contributesToComposite())
            ->map(fn ($instance) => $instance->getCompositeContribution())
            ->filter();

        // Build article body from all contributions that carry text content
        $articleBody = $contributions
            ->filter(fn ($c) => ! empty($c['content']))
            ->pluck('content')
            ->filter()
            ->implode("\n\n");

        // Collect images — single 'url' (text, image, text_with_image) or 'urls' (gallery, carousel, text_with_images)
        $images = $contributions
            ->flatMap(function (array $c): array {
                if (! empty($c['url'])) {
                    return [$c['url']];
                }

                if (! empty($c['urls']) && is_array($c['urls'])) {
                    return $c['urls'];
                }

                return [];
            })
            ->filter()
            ->values()
            ->toArray();

        // Use the SeoData schema_type if it is an Article-family type (BlogPosting, NewsArticle),
        // so the block-derived schema matches what the editor intended and deduplication works.
        $articleTypes = [SchemaType::Article, SchemaType::BlogPosting, SchemaType::NewsArticle];

        $type = in_array($seoData?->schema_type, $articleTypes, true)
            ? $seoData->schema_type->value
            : 'Article';

        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => $type,
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
