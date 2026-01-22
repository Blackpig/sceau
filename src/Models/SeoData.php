<?php

namespace BlackpigCreatif\Sceau\Models;

use BlackpigCreatif\Atelier\Models\AtelierBlock;
use BlackpigCreatif\Sceau\Enums\SchemaType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoData extends Model
{
    protected $table = 'seo_data';

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'title',
        'description',
        'canonical_url',
        'robots_directive',
        'focus_keyword',
        'og_use_hero_image',
        'open_graph',
        'twitter_use_hero_image',
        'twitter_card',
        'schema_type',
        'schema_data',
        'content_updated_at',
        'update_notes',
        'faq_pairs',
    ];

    protected function casts(): array
    {
        return [
            'og_use_hero_image' => 'boolean',
            'open_graph' => 'array',
            'twitter_use_hero_image' => 'boolean',
            'twitter_card' => 'array',
            'schema_type' => SchemaType::class,
            'schema_data' => 'array',
            'faq_pairs' => 'array',
            'content_updated_at' => 'datetime',
        ];
    }

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getOgTitle(): ?string
    {
        return $this->open_graph['title'] ?? $this->title;
    }

    public function getOgDescription(): ?string
    {
        return $this->open_graph['description'] ?? $this->description;
    }

    public function getOgImage(): ?string
    {
        // If "use hero image" toggle is on, try to get hero block image
        if ($this->og_use_hero_image) {
            $heroImage = $this->resolveHeroImage('og');
            if ($heroImage) {
                return $heroImage;
            }
        }

        // Fall back to uploaded OG image
        return $this->resolveRetouchMediaUrl($this->open_graph['image'] ?? null, 'og');
    }

    public function getOgType(): string
    {
        return $this->open_graph['type'] ?? 'website';
    }

    public function getTwitterTitle(): ?string
    {
        return $this->twitter_card['title'] ?? $this->getOgTitle();
    }

    public function getTwitterDescription(): ?string
    {
        return $this->twitter_card['description'] ?? $this->getOgDescription();
    }

    public function getTwitterImage(): ?string
    {
        // If "use hero image" toggle is on, try to get hero block image
        if ($this->twitter_use_hero_image) {
            $heroImage = $this->resolveHeroImage('twitter');
            if ($heroImage) {
                return $heroImage;
            }
        }

        // Fall back to uploaded Twitter image, then OG image
        $twitterImage = $this->resolveRetouchMediaUrl($this->twitter_card['image'] ?? null, 'twitter');

        return $twitterImage ?? $this->getOgImage();
    }

    public function getTwitterCardType(): string
    {
        return $this->twitter_card['card_type'] ?? 'summary_large_image';
    }

    public function hasSchemaMarkup(): bool
    {
        return $this->schema_type !== null && ! empty($this->schema_data);
    }

    public function hasFaqPairs(): bool
    {
        return ! empty($this->faq_pairs);
    }

    /**
     * Resolve hero image from the parent model's blocks
     */
    protected function resolveHeroImage(string $conversion = 'og'): ?string
    {
        // Get the parent model (Page, Post, etc.)
        $parent = $this->seoable;

        if (! $parent) {
            return null;
        }

        // Check if parent has blocks relationship
        if (! method_exists($parent, 'blocks')) {
            return null;
        }

        // Find first published hero block
        $heroBlock = $parent->blocks()
            ->published()
            ->ordered()
            ->get()
            ->first(function (AtelierBlock $block) {
                $instance = $block->hydrateBlock();

                return $instance instanceof \BlackpigCreatif\Atelier\Blocks\HeroBlock;
            });

        if (! $heroBlock) {
            return null;
        }

        // Get background_image from hydrated block instance using the get() method
        $instance = $heroBlock->hydrateBlock();
        $backgroundImage = $instance->get('background_image');

        if (! $backgroundImage) {
            return null;
        }

        return $this->resolveRetouchMediaUrl($backgroundImage, $conversion);
    }

    /**
     * Resolve ChambreNoir retouch media URL
     * Handles both string paths and ChambreNoir JSON format
     */
    protected function resolveRetouchMediaUrl(mixed $media, string $conversion = 'og'): ?string
    {
        if (empty($media)) {
            return null;
        }

        // Simple string path (not processed by ChambreNoir)
        if (is_string($media)) {
            return \Storage::disk('public')->url($media);
        }

        // ChambreNoir JSON format with conversions
        if (is_array($media) && isset($media['conversions'][$conversion])) {
            return \Storage::disk('public')->url($media['conversions'][$conversion]);
        }

        // Fall back to original if conversion doesn't exist
        if (is_array($media) && isset($media['original'])) {
            return \Storage::disk('public')->url($media['original']);
        }

        return null;
    }
}
