<?php

namespace BlackpigCreatif\Sceau\Services;

use BlackpigCreatif\Sceau\Facades\Schema;
use BlackpigCreatif\Sceau\Schemas\Runtime\ArticleSchema;
use Illuminate\Database\Eloquent\Model;

class PageSchemaBuilder
{
    /**
     * Build schemas for a page from its blocks.
     */
    public static function build(Model $page): void
    {
        // Get published blocks if the method exists
        if (! method_exists($page, 'publishedBlocks')) {
            return;
        }

        $blocks = $page->publishedBlocks();

        // Check if this page should have an Article schema
        // (i.e., it has blocks that contribute to composite content)
        $hasArticleContent = $blocks->contains(function ($block) {
            $instance = $block->hydrateBlock();

            return method_exists($instance, 'contributesToComposite')
                && $instance->contributesToComposite();
        });

        // Add Article schema if applicable
        if ($hasArticleContent) {
            Schema::push(ArticleSchema::fromBlocks($blocks, $page->seoData ?? null));
        }

        // Add standalone schemas from individual blocks
        foreach ($blocks as $block) {
            $instance = $block->hydrateBlock();

            // Check if block has standalone schema
            if (method_exists($instance, 'hasStandaloneSchema')
                && $instance->hasStandaloneSchema()
                && method_exists($instance, 'toStandaloneSchema')) {
                if ($schema = $instance->toStandaloneSchema()) {
                    Schema::push($schema);
                }
            }
        }
    }
}
