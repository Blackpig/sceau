<?php

use BlackpigCreatif\Sceau\Enums\SchemaType;
use BlackpigCreatif\Sceau\Models\SeoData;
use BlackpigCreatif\Sceau\Services\JsonLdGenerator;

it('generate returns null when no schema markup', function () {
    $seoData = new SeoData();
    $generator = new JsonLdGenerator();

    expect($generator->generate($seoData))->toBeNull();
});

it('generate returns Article schema with proper structure', function () {
    $seoData = SeoData::create([
        'seoable_type' => 'App\\Models\\Page',
        'seoable_id' => 1,
        'title' => 'Test Article',
        'description' => 'Test Description',
        'schema_type' => SchemaType::Article,
        'schema_data' => ['headline' => 'Custom Headline'],
        'content_updated_at' => now(),
    ]);

    $generator = new JsonLdGenerator();
    $json = $generator->generate($seoData);

    expect($json)->not->toBeNull();

    $decoded = json_decode($json, true);
    expect($decoded)->toBeArray()
        ->and($decoded['@context'])->toBe('https://schema.org')
        ->and($decoded['@type'])->toBe('Article')
        ->and($decoded['headline'])->toBe('Custom Headline');
});

it('generate returns Product schema with proper structure', function () {
    $seoData = SeoData::create([
        'seoable_type' => 'App\\Models\\Product',
        'seoable_id' => 1,
        'title' => 'Test Product',
        'description' => 'Product Description',
        'schema_type' => SchemaType::Product,
        'schema_data' => [],
    ]);

    $generator = new JsonLdGenerator();
    $json = $generator->generate($seoData);

    $decoded = json_decode($json, true);
    expect($decoded['@type'])->toBe('Product')
        ->and($decoded['name'])->toBe('Test Product')
        ->and($decoded['description'])->toBe('Product Description');
});

it('generate returns FAQ schema when faq_pairs exist', function () {
    $seoData = SeoData::create([
        'seoable_type' => 'App\\Models\\Page',
        'seoable_id' => 1,
        'faq_pairs' => [
            ['question' => 'What is SEO?', 'answer' => 'Search Engine Optimization'],
            ['question' => 'Why is SEO important?', 'answer' => 'It helps websites rank better'],
        ],
    ]);

    $generator = new JsonLdGenerator();
    $json = $generator->generate($seoData);

    $decoded = json_decode($json, true);
    expect($decoded)->toBeArray()
        ->and($decoded['@type'])->toBe('FAQPage')
        ->and($decoded['mainEntity'])->toBeArray()
        ->and($decoded['mainEntity'])->toHaveCount(2)
        ->and($decoded['mainEntity'][0]['@type'])->toBe('Question')
        ->and($decoded['mainEntity'][0]['name'])->toBe('What is SEO?');
});

it('generate combines Article and FAQ schemas into array', function () {
    $seoData = SeoData::create([
        'seoable_type' => 'App\\Models\\Page',
        'seoable_id' => 1,
        'title' => 'Test Article',
        'schema_type' => SchemaType::Article,
        'schema_data' => [],
        'faq_pairs' => [
            ['question' => 'What is this?', 'answer' => 'An article'],
        ],
    ]);

    $generator = new JsonLdGenerator();
    $json = $generator->generate($seoData);

    $decoded = json_decode($json, true);
    expect($decoded)->toBeArray()
        ->and($decoded)->toHaveCount(2)
        ->and($decoded[0]['@type'])->toBe('Article')
        ->and($decoded[1]['@type'])->toBe('FAQPage');
});

it('generate uses custom schema_data when provided', function () {
    $seoData = SeoData::create([
        'seoable_type' => 'App\\Models\\Page',
        'seoable_id' => 1,
        'schema_type' => SchemaType::Article,
        'schema_data' => [
            'headline' => 'Custom Headline',
            'author' => [
                '@type' => 'Person',
                'name' => 'John Doe',
            ],
        ],
    ]);

    $generator = new JsonLdGenerator();
    $json = $generator->generate($seoData);

    $decoded = json_decode($json, true);
    expect($decoded['headline'])->toBe('Custom Headline')
        ->and($decoded['author']['name'])->toBe('John Doe');
});

it('generateFaqSchema creates valid FAQPage structure', function () {
    $seoData = new SeoData([
        'faq_pairs' => [
            ['question' => 'Q1', 'answer' => 'A1'],
            ['question' => 'Q2', 'answer' => 'A2'],
        ],
    ]);

    $generator = new JsonLdGenerator();
    $schema = $generator->generateSchemaFromType($seoData);

    // Since schema_type is null, this should return null
    expect($schema)->toBeNull();

    // But we can test the protected method indirectly through generate
    $seoData->faq_pairs = [
        ['question' => 'Q1', 'answer' => 'A1'],
    ];
    $json = $generator->generate($seoData);
    $decoded = json_decode($json, true);

    expect($decoded['mainEntity'][0]['acceptedAnswer']['text'])->toBe('A1');
});

it('JSON output is valid and parseable', function () {
    $seoData = SeoData::create([
        'seoable_type' => 'App\\Models\\Page',
        'seoable_id' => 1,
        'title' => 'Test',
        'schema_type' => SchemaType::Article,
        'schema_data' => [],
    ]);

    $generator = new JsonLdGenerator();
    $json = $generator->generate($seoData);

    expect($json)->toBeString();

    $decoded = json_decode($json, true);
    expect(json_last_error())->toBe(JSON_ERROR_NONE)
        ->and($decoded)->toBeArray();
});
