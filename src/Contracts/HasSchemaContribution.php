<?php

namespace BlackpigCreatif\Sceau\Contracts;

use BlackpigCreatif\Sceau\Enums\SchemaType;

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

    /**
     * Return the SchemaType this block represents for driver-based schema generation.
     * Return null if this block does not declare a typed schema.
     */
    public function getSchemaType(): ?SchemaType;

    /**
     * Return the data payload the driver uses to build the schema array.
     * The expected shape is SchemaType-specific — see SceauBlockSchemaDriver for contracts.
     *
     * @return array<string, mixed>
     */
    public function getSchemaData(): array;
}
