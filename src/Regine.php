<?php

declare(strict_types=1);

namespace Regine;

use Regine\Collections\PatternCollection;
use Regine\Composables\HasAlternation;
use Regine\Composables\HasAnchors;
use Regine\Composables\HasCharacterClasses;
use Regine\Composables\HasGroups;
use Regine\Composables\HasLiterals;
use Regine\Composables\HasQuantifiers;
use Regine\Composables\HasShorthands;

class Regine
{
    use HasAlternation,
        HasAnchors,
        HasCharacterClasses,
        HasGroups,
        HasLiterals,
        HasQuantifiers,
        HasShorthands;

    protected PatternCollection $components;

    public function __construct()
    {
        $this->components = new PatternCollection;
    }

    public static function make(): self
    {
        return new self;
    }

    public function compile(string $delimiter = '/'): string
    {
        return $delimiter . $this->components->compile() . $delimiter;
    }

    /**
     * Compile the pattern without delimiters (raw pattern)
     *
     * This is useful for nesting patterns, Laravel routes, or other contexts
     * where regex delimiters are not needed.
     *
     * <code>
     *      $pattern = Regine::make()->literal('test')->digit()->oneOrMore()->compileRaw(); // test\d+
     *      Route::get("/{$pattern}", function() { ... }); // Laravel route usage
     * </code>
     */
    public function compileRaw(): string
    {
        return $this->components->compile();
    }

    /**
     * Get a human-readable description of the pattern
     */
    public function describe(): string
    {
        return $this->components->describe();
    }

    /**
     * Get metadata about all components for debugging
     *
     * @return array<array<string, mixed>>
     */
    public function getMetadata(): array
    {
        return $this->components->getMetadata();
    }

    /**
     * Get the count of components
     */
    public function getComponentCount(): int
    {
        return $this->components->count();
    }

    /**
     * Check if the pattern is empty
     */
    public function isEmpty(): bool
    {
        return $this->components->isEmpty();
    }

    /**
     * Test the pattern against a subject string
     */
    public function test(string $subject): bool
    {
        return preg_match($this->compile(), $subject) === 1;
    }

    /**
     * Get matches from a subject string
     *
     * @return array<string>
     */
    public function matches(string $subject): array
    {
        preg_match($this->compile(), $subject, $matches);

        return $matches;
    }

    /**
     * Debug method to show the pattern structure
     *
     * @return array<string, mixed>
     */
    public function debug(): array
    {
        return [
            'pattern' => $this->components->compile(),
            'compiled' => $this->compile(),
            'description' => $this->describe(),
            'component_count' => $this->getComponentCount(),
            'metadata' => $this->getMetadata(),
        ];
    }
}
