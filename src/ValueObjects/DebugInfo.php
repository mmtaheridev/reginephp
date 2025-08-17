<?php

declare(strict_types=1);

namespace Regine\ValueObjects;

use Regine\Regine;

/**
 * Immutable debug info object for on-demand inspection of a Regine instance
 */
final class DebugInfo
{
    /**
     * The Regine instance under inspection
     */
    private readonly Regine $regine;

    public function __construct(Regine $regine)
    {
        $this->regine = $regine;
    }

    /**
     * Get pattern without delimiters
     */
    public function pattern(): string
    {
        return $this->regine->compileRaw();
    }

    /**
     * Get compiled pattern with delimiters and flags
     */
    public function compiled(string $delimiter = '/'): string
    {
        return $this->regine->compile($delimiter);
    }

    /**
     * Human-readable description of the pattern
     */
    public function description(): string
    {
        return $this->regine->describe();
    }

    /**
     * Number of elements in the pattern
     */
    public function elementCount(): int
    {
        return $this->regine->getElementCount();
    }

    /**
     * Flags as a compact string (e.g., "imsux")
     */
    public function flags(): string
    {
        return $this->regine->getFlagsString();
    }

    /**
     * Element metadata
     *
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return $this->regine->getMetadata();
    }

    /**
     * Returns a serialzed array of the debug info
     *
     * @return array{pattern: string, compiled: string, description: string, elementCount: int, flags: string, metadata: array<string, mixed>}
     */
    public function toArray(): array
    {
        return [
            'pattern' => $this->pattern(),
            'compiled' => $this->compiled(),
            'description' => $this->description(),
            'elementCount' => $this->elementCount(),
            'flags' => $this->flags(),
            'metadata' => $this->metadata(),
        ];
    }
}
