<?php

namespace BlackpigCreatif\Sceau\Services;

class SchemaStack
{
    /**
     * The schemas that have been pushed during this request.
     */
    protected array $schemas = [];

    /**
     * Push a schema onto the stack.
     */
    public function push(array $schema): static
    {
        $this->schemas[] = $schema;

        return $this;
    }

    /**
     * Get all schemas from the stack.
     */
    public function all(): array
    {
        return $this->schemas;
    }

    /**
     * Clear all schemas from the stack.
     */
    public function clear(): void
    {
        $this->schemas = [];
    }

    /**
     * Check if the stack is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->schemas);
    }

    /**
     * Get the number of schemas in the stack.
     */
    public function count(): int
    {
        return count($this->schemas);
    }
}
