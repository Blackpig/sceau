<?php

namespace BlackpigCreatif\Sceau\Services;

use BlackpigCreatif\Atelier\Contracts\BlockSchemaDriverInterface;
use BlackpigCreatif\Atelier\Contracts\HasCompositeSchema;
use BlackpigCreatif\Atelier\Contracts\HasSchemaContribution;
use BlackpigCreatif\Atelier\Contracts\HasStandaloneSchema;
use BlackpigCreatif\Sceau\Facades\Schema;
use BlackpigCreatif\Sceau\Schemas\Runtime\ArticleSchema;
use Illuminate\Database\Eloquent\Model;

class PageSchemaBuilder
{
    /**
     * Build schemas for a page from its Atelier blocks.
     *
     * Three passes are made over the block collection:
     *   Pass 1 — Composite Article schema (text/image blocks contributing body content)
     *   Pass 2 — Legacy standalone schemas via toStandaloneSchema() (e.g. VideoBlock)
     *   Pass 3 — Driver-based typed schemas via getSchemaType() / getSchemaData()
     *
     * All generated schemas are pushed onto the SchemaStack for the Head component to output.
     */
    public static function build(Model $page): void
    {
        if (! method_exists($page, 'publishedBlocks')) {
            return;
        }

        // Materialise the collection once to avoid repeated query execution across passes
        $blocks = $page->publishedBlocks()->get();

        // Pass 1: Composite Article schema
        $hasArticleContent = $blocks->contains(function ($block) {
            $instance = $block->hydrateBlock();

            return $instance instanceof HasCompositeSchema && $instance->contributesToComposite();
        });

        if ($hasArticleContent) {
            Schema::push(ArticleSchema::fromBlocks($blocks, $page->seoData ?? null));
        }

        // Resolve driver once before the per-block loop
        $driver = static::resolveDriver();

        // Pass 2 + Pass 3: per-block schemas
        foreach ($blocks as $block) {
            $instance = $block->hydrateBlock();

            // Pass 2: legacy hand-crafted standalone schema (e.g. VideoBlock)
            if ($instance instanceof HasStandaloneSchema && $instance->hasStandaloneSchema()) {
                if ($schema = $instance->toStandaloneSchema()) {
                    Schema::push($schema);
                }
            }

            // Pass 3: driver-based typed schema (e.g. FaqsBlock)
            if ($driver !== null && $instance instanceof HasSchemaContribution) {
                if ($schema = $driver->resolveSchema($instance)) {
                    Schema::push($schema);
                }
            }
        }
    }

    /**
     * Resolve the configured block schema driver from the container.
     * Returns null when Atelier is not installed, the driver is not configured,
     * or the driver class does not exist.
     */
    protected static function resolveDriver(): ?BlockSchemaDriverInterface
    {
        if (! app()->bound(BlockSchemaDriverInterface::class)) {
            return null;
        }

        try {
            return app(BlockSchemaDriverInterface::class);
        } catch (\Throwable) {
            return null;
        }
    }
}
