<?php

use BlackpigCreatif\Sceau\Enums\SchemaType;
use BlackpigCreatif\Sceau\Models\SeoData;
use BlackpigCreatif\Sceau\SchemaGenerators\FaqSchema;
use BlackpigCreatif\Sceau\Services\JsonLdGenerator;

it('generate returns null when no schema markup', function () {
    expect((new JsonLdGenerator)->generate(new SeoData))->toBeNull();
});

it('generate returns Article schema with proper structure', function () {
    $seoData = new SeoData([
        'title' => 'Test Article',
        'description' => 'Test Description',
        'schema_type' => SchemaType::Article,
    ]);

    $decoded = json_decode((new JsonLdGenerator)->generate($seoData), true);

    expect($decoded['@context'])->toBe('https://schema.org')
        ->and($decoded['@type'])->toBe('Article')
        ->and($decoded['headline'])->toBe('Test Article')
        ->and($decoded['description'])->toBe('Test Description');
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

    $decoded = json_decode((new JsonLdGenerator)->generate($seoData), true);

    expect($decoded['@type'])->toBe('Product')
        ->and($decoded['name'])->toBe('Test Product')
        ->and($decoded['description'])->toBe('Product Description');
});

it('generate uses custom schema_data when provided', function () {
    $seoData = new SeoData([
        'schema_type' => SchemaType::Article,
        'schema_data' => json_encode([
            'headline' => 'Custom Headline',
            'author' => ['@type' => 'Person', 'name' => 'John Doe'],
        ]),
    ]);

    $decoded = json_decode((new JsonLdGenerator)->generate($seoData), true);

    expect($decoded['@type'])->toBe('Article')
        ->and($decoded['headline'])->toBe('Custom Headline')
        ->and($decoded['author']['name'])->toBe('John Doe');
});

it('generate returns null for an FAQPage without custom schema_data', function () {
    $seoData = new SeoData(['schema_type' => SchemaType::FAQPage]);

    expect((new JsonLdGenerator)->generate($seoData))->toBeNull();
});

it('generate uses custom schema_data for an FAQPage', function () {
    $seoData = new SeoData([
        'schema_type' => SchemaType::FAQPage,
        'schema_data' => json_encode(['mainEntity' => [['@type' => 'Question', 'name' => 'Q1']]]),
    ]);

    $decoded = json_decode((new JsonLdGenerator)->generate($seoData), true);

    expect($decoded['@type'])->toBe('FAQPage')
        ->and($decoded['mainEntity'][0]['name'])->toBe('Q1');
});

it('FaqSchema::fromPairs builds a valid FAQPage structure', function () {
    $schema = FaqSchema::fromPairs([
        ['question' => 'Q1', 'answer' => 'A1'],
        ['question' => 'Q2', 'answer' => 'A2'],
    ]);

    expect($schema['@type'])->toBe('FAQPage')
        ->and($schema['mainEntity'])->toHaveCount(2)
        ->and($schema['mainEntity'][0]['@type'])->toBe('Question')
        ->and($schema['mainEntity'][0]['name'])->toBe('Q1')
        ->and($schema['mainEntity'][0]['acceptedAnswer']['text'])->toBe('A1');
});

it('JSON output is valid and parseable', function () {
    $seoData = new SeoData([
        'title' => 'Test',
        'schema_type' => SchemaType::Article,
    ]);

    $json = (new JsonLdGenerator)->generate($seoData);

    json_decode($json, true);

    expect($json)->toBeString()
        ->and(json_last_error())->toBe(JSON_ERROR_NONE);
});
