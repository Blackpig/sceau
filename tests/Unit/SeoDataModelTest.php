<?php

use BlackpigCreatif\Sceau\Enums\SchemaType;
use BlackpigCreatif\Sceau\Models\SeoData;
use Illuminate\Support\Carbon;

it('casts schema_type to SchemaType enum', function () {
    $seoData = SeoData::create([
        'seoable_type' => 'App\\Models\\Page',
        'seoable_id' => 1,
        'schema_type' => SchemaType::Article,
    ]);

    expect($seoData->fresh()->schema_type)->toBe(SchemaType::Article);
});

it('stores schema_data as a JSON string per locale', function () {
    $seoData = SeoData::create([
        'seoable_type' => 'App\\Models\\Page',
        'seoable_id' => 1,
        'schema_data' => ['en' => '{"headline":"English"}', 'fr' => '{"headline":"Français"}'],
    ])->fresh();

    expect($seoData->schema_data)->toBe('{"headline":"English"}')
        ->and($seoData->getTranslation('schema_data', 'fr'))->toBe('{"headline":"Français"}');
});

it('casts content_updated_at to datetime', function () {
    $seoData = SeoData::create([
        'seoable_type' => 'App\\Models\\Page',
        'seoable_id' => 1,
        'content_updated_at' => '2024-01-15 10:30:00',
    ]);

    expect($seoData->content_updated_at)->toBeInstanceOf(Carbon::class);
});

it('getOgTitle falls back to the meta title', function () {
    expect((new SeoData(['title' => 'Main Title']))->getOgTitle())->toBe('Main Title');
});

it('getOgTitle prefers og_title when set', function () {
    $seoData = new SeoData(['title' => 'Main Title', 'og_title' => 'OG Title']);

    expect($seoData->getOgTitle())->toBe('OG Title');
});

it('getOgDescription falls back to the meta description', function () {
    expect((new SeoData(['description' => 'Main Description']))->getOgDescription())->toBe('Main Description');
});

it('getOgDescription prefers og_description when set', function () {
    $seoData = new SeoData(['description' => 'Main Description', 'og_description' => 'OG Description']);

    expect($seoData->getOgDescription())->toBe('OG Description');
});

it('getTwitterTitle falls back to the OG title', function () {
    $seoData = new SeoData(['title' => 'Main Title', 'og_title' => 'OG Title']);

    expect($seoData->getTwitterTitle())->toBe('OG Title');
});

it('getTwitterTitle prefers twitter_title when set', function () {
    $seoData = new SeoData(['og_title' => 'OG Title', 'twitter_title' => 'Twitter Title']);

    expect($seoData->getTwitterTitle())->toBe('Twitter Title');
});

it('getTwitterDescription falls back to the OG description', function () {
    $seoData = new SeoData(['description' => 'Main Description', 'og_description' => 'OG Description']);

    expect($seoData->getTwitterDescription())->toBe('OG Description');
});

it('getTwitterDescription prefers twitter_description when set', function () {
    $seoData = new SeoData(['og_description' => 'OG Description', 'twitter_description' => 'Twitter Description']);

    expect($seoData->getTwitterDescription())->toBe('Twitter Description');
});

it('hasSchemaMarkup returns true when schema_type and schema_data exist', function () {
    $seoData = new SeoData([
        'schema_type' => SchemaType::Article,
        'schema_data' => '{"headline":"Test"}',
    ]);

    expect($seoData->hasSchemaMarkup())->toBeTrue();
});

it('hasSchemaMarkup returns false when either is missing', function () {
    expect((new SeoData(['schema_type' => SchemaType::Article]))->hasSchemaMarkup())->toBeFalse()
        ->and((new SeoData(['schema_data' => '{"headline":"Test"}']))->hasSchemaMarkup())->toBeFalse();
});
