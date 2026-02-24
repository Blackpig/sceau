<?php

namespace BlackpigCreatif\Sceau\SchemaGenerators;

use BlackpigCreatif\Sceau\Models\SeoData;

class RecipeSchema extends BaseSchema
{
    public function getType(): string
    {
        return 'Recipe';
    }

    public function generate(SeoData $seoData): array
    {
        $schema = $this->baseSchema();

        $schema['name'] = $seoData->title ?? config('app.name');

        if ($description = $this->getDescription($seoData)) {
            $schema['description'] = $description;
        }

        if ($image = $this->getImage($seoData)) {
            $schema['image'] = $image;
        }

        // Author from parent model or settings
        $settings = $this->getSettings();
        $parent = $seoData->seoable;

        if ($parent && method_exists($parent, 'author') && $parent->author) {
            $schema['author'] = [
                '@type' => 'Person',
                'name'  => $parent->author->name ?? null,
            ];
        } elseif ($settings?->site_name) {
            $schema['author'] = [
                '@type' => 'Organization',
                'name'  => $settings->site_name,
            ];
        }

        // Date published
        if ($parent?->created_at) {
            $schema['datePublished'] = $parent->created_at->toIso8601String();
        }

        return $this->removeNullValues($schema);
    }

    public function getSkeleton(): array
    {
        return [
            '@context'         => 'https://schema.org',
            '@type'            => 'Recipe',
            'name'             => '',
            'image'            => [],
            'description'      => '',
            'keywords'         => '',
            'author'           => [
                '@type' => 'Person',
                'name'  => '',
            ],
            'datePublished'    => '',
            'prepTime'         => '',   // ISO 8601 duration, e.g. PT15M
            'cookTime'         => '',   // ISO 8601 duration, e.g. PT1H
            'totalTime'        => '',   // ISO 8601 duration, e.g. PT1H15M
            'recipeCategory'   => '',   // e.g. "Dessert"
            'recipeCuisine'    => '',   // e.g. "French"
            'recipeYield'      => '',   // e.g. "4 servings"
            'nutrition'        => [
                '@type'   => 'NutritionInformation',
                'calories' => '',       // e.g. "270 calories"
            ],
            'recipeIngredient' => [
                '',
            ],
            'recipeInstructions' => [
                [
                    '@type' => 'HowToStep',
                    'name'  => '',
                    'text'  => '',
                    'url'   => '',
                    'image' => '',
                ],
            ],
            'aggregateRating'  => [
                '@type'       => 'AggregateRating',
                'ratingValue' => '',
                'ratingCount' => '',
            ],
            'video' => [
                '@type'       => 'VideoObject',
                'name'        => '',
                'description' => '',
                'thumbnailUrl' => [],
                'contentUrl'  => '',
                'embedUrl'    => '',
                'uploadDate'  => '',
            ],
        ];
    }
}
