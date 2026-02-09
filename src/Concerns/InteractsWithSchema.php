<?php

namespace BlackpigCreatif\Sceau\Concerns;

/**
 * Trait for Atelier blocks to contribute to Schema.org structured data.
 *
 * By default, blocks don't contribute to any schemas.
 * Override methods as needed for your block type.
 */
trait InteractsWithSchema
{
    /**
     * Does this block contribute to a composite schema (like Article)?
     */
    public function contributesToComposite(): bool
    {
        return false;
    }

    /**
     * Get data to contribute to composite schema.
     */
    public function getCompositeContribution(): ?array
    {
        return null;
    }

    /**
     * Does this block generate its own standalone schema?
     */
    public function hasStandaloneSchema(): bool
    {
        return false;
    }

    /**
     * Get standalone schema array.
     */
    public function toStandaloneSchema(): ?array
    {
        return null;
    }
}
