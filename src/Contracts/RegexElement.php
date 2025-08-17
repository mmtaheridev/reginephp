<?php

declare(strict_types=1);

namespace Regine\Contracts;

/**
 * Base interface for all regex elements (components and decorators).
 *
 * This interface defines the core functionality that all regex elements must provide,
 * whether they are content elements (components) or modification elements (decorators).
 */
interface RegexElement
{
    /**
     * Compile the element to its regex string representation.
     */
    public function compile(): string;

    /**
     * Get the type identifier for this element.
     */
    public function getType(): string;

    /**
     * Get metadata about this element for debugging and introspection.
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array;

    /**
     * Check if this element can be quantified.
     */
    public function canBeQuantified(): bool;

    /**
     * Get a human-readable description of this element.
     */
    public function getDescription(): string;
}
