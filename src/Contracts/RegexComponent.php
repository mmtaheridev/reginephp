<?php

declare(strict_types=1);

namespace Regine\Contracts;

interface RegexComponent
{
    /**
     * Compile the component to its regex string representation
     */
    public function compile(): string;

    /**
     * Get the type of this component (for introspection)
     */
    public function getType(): string;

    /**
     * Get metadata about this component (for debugging/introspection)
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array;

    /**
     * Check if this component can be quantified
     */
    public function canBeQuantified(): bool;

    /**
     * Get a human-readable description of this component
     */
    public function getDescription(): string;
}
