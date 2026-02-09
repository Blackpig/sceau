<?php

namespace BlackpigCreatif\Sceau\SchemaGenerators;

use BlackpigCreatif\Sceau\Models\SeoData;

abstract class BaseSchema
{
    /**
     * Generate the schema array for the given SEO data.
     */
    abstract public function generate(SeoData $seoData): array;

    /**
     * Get the schema type name.
     */
    abstract public function getType(): string;

    /**
     * Get the base schema structure with @context and @type.
     */
    protected function baseSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => $this->getType(),
        ];
    }

    /**
     * Get name from SEO data or fallback to app name.
     */
    protected function getName(SeoData $seoData): string
    {
        return $seoData->og_site_name ?? config('app.name');
    }

    /**
     * Get URL from SEO data or fallback to app URL.
     */
    protected function getUrl(SeoData $seoData): string
    {
        return $seoData->canonical_url ?? config('app.url');
    }

    /**
     * Get description from SEO data.
     * Uses current locale for translated fields.
     */
    protected function getDescription(SeoData $seoData): ?string
    {
        $locale = app()->getLocale();

        return $seoData->getTranslation('description', $locale, false);
    }

    /**
     * Get image from SEO data.
     */
    protected function getImage(SeoData $seoData): ?string
    {
        return $seoData->getOgImage();
    }

    /**
     * Get settings model instance.
     */
    protected function getSettings()
    {
        return \BlackpigCreatif\Sceau\Models\SeoSettings::first();
    }

    /**
     * Remove null values from array recursively.
     */
    protected function removeNullValues(array $array): array
    {
        return array_filter($array, function ($value) {
            if (is_array($value)) {
                return ! empty($this->removeNullValues($value));
            }

            return $value !== null;
        });
    }

    /**
     * Get a skeleton schema structure for this type.
     * Returns a basic schema template with empty values.
     */
    public function getSkeleton(): array
    {
        return $this->baseSchema();
    }
}
