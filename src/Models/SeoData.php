<?php

namespace BlackpigCreatif\Sceau\Models;

use BlackpigCreatif\Atelier\Models\AtelierBlock;
use BlackpigCreatif\ChambreNoir\StateCasts\RetouchMediaUploadStateCast;
use BlackpigCreatif\Sceau\Enums\SchemaType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

class SeoData extends Model
{
    use HasTranslations;

    protected $table = 'sceau_seo_data';

    public array $translatable = [
        'title',
        'description',
        'focus_keyword',
        'og_title',
        'og_description',
        'twitter_title',
        'twitter_description',
        'schema_data',
    ];

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'title',
        'description',
        'canonical_url',
        'robots_directive',
        'focus_keyword',
        'og_use_hero_image',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'og_site_name',
        'og_locale',
        'twitter_title',
        'twitter_description',
        'twitter_card_type',
        'twitter_site',
        'twitter_creator',
        'schema_type',
        'schema_data',
        'content_updated_at',
        'update_notes',
    ];

    protected function casts(): array
    {
        return [
            'og_use_hero_image' => 'boolean',
            'og_title' => 'array',
            'og_description' => 'array',
            'og_image' => 'array',
            'twitter_title' => 'array',
            'twitter_description' => 'array',
            'schema_type' => SchemaType::class,
            'schema_data' => 'array',
            'content_updated_at' => 'datetime',
        ];
    }

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get OG title with fallback to meta title
     */
    public function getOgTitle(): ?string
    {
        $locale = app()->getLocale();
        $ogTitle = $this->getTranslation('og_title', $locale, true);
        $title = $this->getTranslation('title', $locale, true);

        return $ogTitle ?? $title;
    }

    /**
     * Get OG description with fallback to meta description
     */
    public function getOgDescription(): ?string
    {
        $locale = app()->getLocale();
        $ogDescription = $this->getTranslation('og_description', $locale, true);
        $description = $this->getTranslation('description', $locale, true);

        return $ogDescription ?? $description;
    }

    /**
     * Get OG image
     */
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
        return $this->resolveRetouchMediaUrl($this->og_image, 'og');
    }

    /**
     * Get OG type
     */
    public function getOgType(): string
    {
        return $this->og_type ?? 'website';
    }

    /**
     * Get Twitter title with fallback to OG title
     */
    public function getTwitterTitle(): ?string
    {
        $locale = app()->getLocale();
        $twitterTitle = $this->getTranslation('twitter_title', $locale, true);

        return $twitterTitle ?? $this->getOgTitle();
    }

    /**
     * Get Twitter description with fallback to OG description
     */
    public function getTwitterDescription(): ?string
    {
        $locale = app()->getLocale();
        $twitterDescription = $this->getTranslation('twitter_description', $locale, true);

        return $twitterDescription ?? $this->getOgDescription();
    }

    /**
     * Get Twitter image (uses OG image with Twitter conversion)
     */
    public function getTwitterImage(): ?string
    {
        // If "use hero image" toggle is on, try to get hero block image
        if ($this->og_use_hero_image) {
            $heroImage = $this->resolveHeroImage('twitter');
            if ($heroImage) {
                return $heroImage;
            }
        }

        // Use the social image with Twitter conversion
        return $this->resolveRetouchMediaUrl($this->og_image, 'twitter');
    }

    /**
     * Get Twitter card type
     */
    public function getTwitterCardType(): string
    {
        return $this->twitter_card_type ?? 'summary_large_image';
    }

    public function hasSchemaMarkup(): bool
    {
        return $this->schema_type !== null && ! empty($this->schema_data);
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
