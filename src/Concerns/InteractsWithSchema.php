<?php

namespace BlackpigCreatif\Sceau\Concerns;

/**
 * Provides default no-op implementations of the Atelier schema contracts.
 *
 * Retained for backwards compatibility. New blocks should rely on the default
 * implementations provided by BaseBlock (HasCompositeSchema, HasStandaloneSchema,
 * HasSchemaContribution) directly rather than using this trait.
 *
 * @deprecated Use BaseBlock's built-in contract implementations instead.
 */
trait InteractsWithSchema
{
    public function contributesToComposite(): bool
    {
        return false;
    }

    public function getCompositeContribution(): ?array
    {
        return null;
    }

    public function hasStandaloneSchema(): bool
    {
        return false;
    }

    public function toStandaloneSchema(): ?array
    {
        return null;
    }

    public function getSchemaType(): ?\BackedEnum
    {
        return null;
    }

    /** @return array<string, mixed> */
    public function getSchemaData(): array
    {
        return [];
    }
}
