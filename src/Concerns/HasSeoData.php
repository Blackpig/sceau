<?php

namespace BlackpigCreatif\Sceau\Concerns;

use BlackpigCreatif\Sceau\Models\SeoData;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeoData
{
    public function seoData(): MorphOne
    {
        return $this->morphOne(SeoData::class, 'seoable');
    }

    public function getSeoData(): ?SeoData
    {
        return $this->seoData;
    }

    public function hasSeoData(): bool
    {
        return $this->seoData !== null;
    }

    public function getSeoAttribute(string $attribute, mixed $fallback = null): mixed
    {
        return $this->seoData?->{$attribute} ?? $fallback;
    }

    public function getSeoTitle(): ?string
    {
        return $this->seoData?->title
            ?? $this->title
            ?? $this->name
            ?? null;
    }

    public function getSeoDescription(): ?string
    {
        return $this->seoData?->description ?? null;
    }
}
