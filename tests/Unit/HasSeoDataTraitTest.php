<?php

use BlackpigCreatif\Sceau\Concerns\HasSeoData;
use BlackpigCreatif\Sceau\Models\SeoData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    // Create a test_models table for testing
    Schema::create('test_models', function (Blueprint $table) {
        $table->id();
        $table->string('title')->nullable();
        $table->string('name')->nullable();
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('test_models');
});

// Test Model class
class TestModel extends Model
{
    use HasSeoData;

    protected $fillable = ['title', 'name'];
}

it('establishes morphOne relationship to SeoData', function () {
    $model = TestModel::create(['title' => 'Test']);

    $seoData = $model->seoData()->create([
        'title' => 'SEO Title',
        'description' => 'SEO Description',
    ]);

    expect($model->seoData)->toBeInstanceOf(SeoData::class)
        ->and($model->seoData->id)->toBe($seoData->id);
});

it('getSeoData returns SeoData when exists', function () {
    $model = TestModel::create(['title' => 'Test']);
    $model->seoData()->create(['title' => 'SEO Title']);

    expect($model->getSeoData())->toBeInstanceOf(SeoData::class);
});

it('getSeoData returns null when not exists', function () {
    $model = TestModel::create(['title' => 'Test']);

    expect($model->getSeoData())->toBeNull();
});

it('hasSeoData returns true when relationship exists', function () {
    $model = TestModel::create(['title' => 'Test']);
    $model->seoData()->create(['title' => 'SEO Title']);

    // Reload to ensure relationship is loaded
    $model = $model->fresh();

    expect($model->hasSeoData())->toBeTrue();
});

it('hasSeoData returns false when relationship does not exist', function () {
    $model = TestModel::create(['title' => 'Test']);

    expect($model->hasSeoData())->toBeFalse();
});

it('getSeoTitle returns SEO title when available', function () {
    $model = TestModel::create(['title' => 'Model Title']);
    $model->seoData()->create(['title' => 'SEO Title']);

    // Reload to load relationship
    $model = $model->fresh();

    expect($model->getSeoTitle())->toBe('SEO Title');
});

it('getSeoTitle returns model title as fallback', function () {
    $model = TestModel::create(['title' => 'Model Title']);

    expect($model->getSeoTitle())->toBe('Model Title');
});

it('getSeoTitle returns model name as fallback', function () {
    $model = TestModel::create(['name' => 'Model Name']);

    expect($model->getSeoTitle())->toBe('Model Name');
});

it('getSeoDescription returns SEO description when available', function () {
    $model = TestModel::create(['title' => 'Test']);
    $model->seoData()->create(['description' => 'SEO Description']);

    // Reload to load relationship
    $model = $model->fresh();

    expect($model->getSeoDescription())->toBe('SEO Description');
});
