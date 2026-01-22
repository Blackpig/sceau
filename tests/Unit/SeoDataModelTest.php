<?php

use BlackpigCreatif\Sceau\Enums\SchemaType;
use BlackpigCreatif\Sceau\Models\SeoData;

it('casts open_graph to array', function () {
    $seoData = new SeoData();
    $seoData->open_graph = ['title' => 'Test'];

    expect($seoData->open_graph)->toBeArray()
        ->and($seoData->open_graph['title'])->toBe('Test');
});

it('casts twitter_card to array', function () {
    $seoData = new SeoData();
    $seoData->twitter_card = ['title' => 'Test'];

    expect($seoData->twitter_card)->toBeArray()
        ->and($seoData->twitter_card['title'])->toBe('Test');
});

it('casts schema_type to SchemaType enum', function () {
    $seoData = SeoData::create([
        'seoable_type' => 'App\\Models\\Page',
        'seoable_id' => 1,
        'schema_type' => SchemaType::Article,
    ]);

    expect($seoData->schema_type)->toBeInstanceOf(SchemaType::class)
        ->and($seoData->schema_type)->toBe(SchemaType::Article);
});

it('casts schema_data to array', function () {
    $seoData = new SeoData();
    $seoData->schema_data = ['headline' => 'Test Headline'];

    expect($seoData->schema_data)->toBeArray()
        ->and($seoData->schema_data['headline'])->toBe('Test Headline');
});

it('casts faq_pairs to array', function () {
    $seoData = new SeoData();
    $seoData->faq_pairs = [
        ['question' => 'Q1', 'answer' => 'A1'],
    ];

    expect($seoData->faq_pairs)->toBeArray()
        ->and($seoData->faq_pairs[0]['question'])->toBe('Q1');
});

it('casts content_updated_at to datetime', function () {
    $seoData = SeoData::create([
        'seoable_type' => 'App\\Models\\Page',
        'seoable_id' => 1,
        'content_updated_at' => '2024-01-15 10:30:00',
    ]);

    expect($seoData->content_updated_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

it('getOgTitle returns fallback to title', function () {
    $seoData = new SeoData(['title' => 'Main Title']);

    expect($seoData->getOgTitle())->toBe('Main Title');

    $seoData->open_graph = ['title' => 'OG Title'];
    expect($seoData->getOgTitle())->toBe('OG Title');
});

it('getOgDescription returns fallback to description', function () {
    $seoData = new SeoData(['description' => 'Main Description']);

    expect($seoData->getOgDescription())->toBe('Main Description');

    $seoData->open_graph = ['description' => 'OG Description'];
    expect($seoData->getOgDescription())->toBe('OG Description');
});

it('getTwitterTitle returns fallback to OG title', function () {
    $seoData = new SeoData([
        'title' => 'Main Title',
        'open_graph' => ['title' => 'OG Title'],
    ]);

    expect($seoData->getTwitterTitle())->toBe('OG Title');

    $seoData->twitter_card = ['title' => 'Twitter Title'];
    expect($seoData->getTwitterTitle())->toBe('Twitter Title');
});

it('getTwitterDescription returns fallback to OG description', function () {
    $seoData = new SeoData([
        'description' => 'Main Description',
        'open_graph' => ['description' => 'OG Description'],
    ]);

    expect($seoData->getTwitterDescription())->toBe('OG Description');

    $seoData->twitter_card = ['description' => 'Twitter Description'];
    expect($seoData->getTwitterDescription())->toBe('Twitter Description');
});

it('hasSchemaMarkup returns true when schema_type and schema_data exist', function () {
    $seoData = new SeoData([
        'schema_type' => SchemaType::Article,
        'schema_data' => ['headline' => 'Test'],
    ]);

    expect($seoData->hasSchemaMarkup())->toBeTrue();
});

it('hasSchemaMarkup returns false when either is missing', function () {
    $seoData = new SeoData(['schema_type' => SchemaType::Article]);
    expect($seoData->hasSchemaMarkup())->toBeFalse();

    $seoData = new SeoData(['schema_data' => ['headline' => 'Test']]);
    expect($seoData->hasSchemaMarkup())->toBeFalse();
});

it('hasFaqPairs returns true when faq_pairs is not empty', function () {
    $seoData = new SeoData([
        'faq_pairs' => [
            ['question' => 'Q1', 'answer' => 'A1'],
        ],
    ]);

    expect($seoData->hasFaqPairs())->toBeTrue();
});

it('hasFaqPairs returns false when faq_pairs is empty', function () {
    $seoData = new SeoData();
    expect($seoData->hasFaqPairs())->toBeFalse();

    $seoData->faq_pairs = [];
    expect($seoData->hasFaqPairs())->toBeFalse();
});
