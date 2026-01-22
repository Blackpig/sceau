<?php

namespace BlackpigCreatif\Sceau\Services;

use BlackpigCreatif\Sceau\Enums\SchemaType;
use BlackpigCreatif\Sceau\Models\SeoData;
use BlackpigCreatif\Sceau\SchemaGenerators\ArticleSchema;
use BlackpigCreatif\Sceau\SchemaGenerators\BaseSchema;
use BlackpigCreatif\Sceau\SchemaGenerators\BlogPostingSchema;
use BlackpigCreatif\Sceau\SchemaGenerators\FaqSchema;
use BlackpigCreatif\Sceau\SchemaGenerators\LocalBusinessSchema;
use BlackpigCreatif\Sceau\SchemaGenerators\NewsArticleSchema;
use BlackpigCreatif\Sceau\SchemaGenerators\OrganizationSchema;
use BlackpigCreatif\Sceau\SchemaGenerators\ProductSchema;

class JsonLdGenerator
{
    /**
     * Schema generator registry.
     */
    protected array $generators = [];

    public function __construct()
    {
        $this->registerDefaultGenerators();
    }

    /**
     * Generate JSON-LD structured data from SEO data.
     */
    public function generate(SeoData $seoData): ?string
    {
        $schemas = [];

        // Add schema from schema_type if exists
        if ($schemaFromType = $this->generateSchemaFromType($seoData)) {
            $schemas[] = $schemaFromType;
        }

        // Add FAQ schema if FAQ pairs exist
        if ($seoData->hasFaqPairs()) {
            $schemas[] = $this->getGenerator(SchemaType::FAQPage)->generate($seoData);
        }

        if (empty($schemas)) {
            return null;
        }

        // If only one schema, return it directly; otherwise return as array
        $output = count($schemas) === 1 ? $schemas[0] : $schemas;

        return json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate schema from the schema_type field.
     */
    public function generateSchemaFromType(SeoData $seoData): ?array
    {
        if ($seoData->schema_type === null) {
            return null;
        }

        // If custom schema_data is provided, merge it with base schema
        if (! empty($seoData->schema_data)) {
            return array_merge([
                '@context' => 'https://schema.org',
                '@type' => $seoData->schema_type->value,
            ], $seoData->schema_data);
        }

        // Use registered generator
        $generator = $this->getGenerator($seoData->schema_type);

        if ($generator) {
            return $generator->generate($seoData);
        }

        // Fallback to basic schema
        return [
            '@context' => 'https://schema.org',
            '@type' => $seoData->schema_type->value,
        ];
    }

    /**
     * Register a schema generator for a given type.
     */
    public function registerGenerator(SchemaType $type, BaseSchema $generator): void
    {
        $this->generators[$type->value] = $generator;
    }

    /**
     * Get generator for a given schema type.
     */
    public function getGenerator(SchemaType $type): ?BaseSchema
    {
        return $this->generators[$type->value] ?? null;
    }

    /**
     * Register default schema generators.
     */
    protected function registerDefaultGenerators(): void
    {
        $this->registerGenerator(SchemaType::Article, new ArticleSchema);
        $this->registerGenerator(SchemaType::BlogPosting, new BlogPostingSchema);
        $this->registerGenerator(SchemaType::NewsArticle, new NewsArticleSchema);
        $this->registerGenerator(SchemaType::Product, new ProductSchema);
        $this->registerGenerator(SchemaType::Organization, new OrganizationSchema);
        $this->registerGenerator(SchemaType::LocalBusiness, new LocalBusinessSchema);
        $this->registerGenerator(SchemaType::FAQPage, new FaqSchema);
    }
}
