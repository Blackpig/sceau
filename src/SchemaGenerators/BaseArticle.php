<?php

namespace BlackpigCreatif\Sceau\SchemaGenerators;

use BlackpigCreatif\Sceau\Models\SeoData;

abstract class BaseArticle extends BaseSchema
{
    public function generate(SeoData $seoData): array
    {
        $schema = $this->baseSchema();

        // Headline (required for articles)
        if ($seoData->title) {
            $schema['headline'] = $seoData->title;
        }

        // Description
        if ($description = $this->getDescription($seoData)) {
            $schema['description'] = $description;
        }

        // Image
        if ($image = $this->getImage($seoData)) {
            $schema['image'] = $image;
        }

        // Author
        if ($author = $this->getAuthor($seoData)) {
            $schema['author'] = $author;
        }

        // Publisher
        if ($publisher = $this->getPublisher($seoData)) {
            $schema['publisher'] = $publisher;
        }

        // Date published
        if ($datePublished = $this->getDatePublished($seoData)) {
            $schema['datePublished'] = $datePublished;
        }

        // Date modified
        if ($dateModified = $this->getDateModified($seoData)) {
            $schema['dateModified'] = $dateModified;
        }

        // Remove null values
        return $this->removeNullValues($schema);
    }

    /**
     * Get the author(s) for the article.
     * Override this method to customize author data.
     */
    protected function getAuthor(SeoData $seoData): ?array
    {
        // Default: Try to get author from the parent model
        $parent = $seoData->seoable;

        if (! $parent) {
            return null;
        }

        // Check if model has author relationship
        if (method_exists($parent, 'author') && $parent->author) {
            return [
                '@type' => 'Person',
                'name' => $parent->author->name ?? null,
                'url' => $parent->author->url ?? null,
            ];
        }

        // Check for author_name attribute
        if (isset($parent->author_name)) {
            return [
                '@type' => 'Person',
                'name' => $parent->author_name,
            ];
        }

        return null;
    }

    /**
     * Get the publisher for the article.
     * Override this method to customize publisher data.
     */
    protected function getPublisher(SeoData $seoData): ?array
    {
        $settings = $this->getSettings();

        $name = $settings?->site_name ?? config('app.name');
        $logo = $this->getImage($seoData);

        if (! $name) {
            return null;
        }

        $publisher = [
            '@type' => 'Organization',
            'name' => $name,
        ];

        if ($logo) {
            $publisher['logo'] = [
                '@type' => 'ImageObject',
                'url' => $logo,
            ];
        }

        return $publisher;
    }

    /**
     * Get the date published for the article.
     * Override this method to customize date published logic.
     */
    protected function getDatePublished(SeoData $seoData): ?string
    {
        $parent = $seoData->seoable;

        if (! $parent) {
            return null;
        }

        // Try created_at or published_at
        if (isset($parent->published_at)) {
            return $parent->published_at->toIso8601String();
        }

        if (isset($parent->created_at)) {
            return $parent->created_at->toIso8601String();
        }

        return null;
    }

    /**
     * Get the date modified for the article.
     * Override this method to customize date modified logic.
     */
    protected function getDateModified(SeoData $seoData): ?string
    {
        // Prefer content_updated_at from SEO data
        if ($seoData->content_updated_at) {
            return $seoData->content_updated_at->toIso8601String();
        }

        $parent = $seoData->seoable;

        if (! $parent) {
            return null;
        }

        // Fall back to updated_at
        if (isset($parent->updated_at)) {
            return $parent->updated_at->toIso8601String();
        }

        return null;
    }

    /**
     * Get a skeleton schema structure for article types.
     */
    public function getSkeleton(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => $this->getType(),
            'headline' => '',
            'image' => [],
            'datePublished' => '',
            'dateModified' => '',
            'author' => [
                [
                    '@type' => 'Person',
                    'name' => '',
                    'url' => '',
                ],
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => '',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => '',
                ],
            ],
        ];
    }
}
