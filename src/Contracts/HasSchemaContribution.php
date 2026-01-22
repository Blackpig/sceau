<?php

namespace BlackpigCreatif\Sceau\Contracts;

interface HasSchemaContribution
{
    /**
     * Does this block contribute to a composite schema (like Article)?
     * Blocks that contribute include text blocks, image blocks, etc.
     */
    public function contributesToComposite(): bool;

    /**
     * Get data to contribute to composite schema.
     * Returns array with 'type' and relevant data.
     * Example: ['type' => 'text', 'content' => '...']
     */
    public function getCompositeContribution(): ?array;

    /**
     * Does this block generate its own standalone schema?
     * Blocks that generate standalone schemas include FAQ, Video, Product Carousel, etc.
     */
    public function hasStandaloneSchema(): bool;

    /**
     * Get standalone schema array.
     * Should return a complete Schema.org schema array.
     */
    public function toStandaloneSchema(): ?array;
}
