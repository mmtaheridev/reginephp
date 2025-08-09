<?php

declare(strict_types=1);

namespace Regine;

use Regine\Collections\PatternCollection;
use Regine\Composables\HasAlternation;
use Regine\Composables\HasAnchors;
use Regine\Composables\HasCharacterClasses;
use Regine\Composables\HasFlags;
use Regine\Composables\HasGroups;
use Regine\Composables\HasLiterals;
use Regine\Composables\HasLookarounds;
use Regine\Composables\HasQuantifiers;
use Regine\Composables\HasShorthands;
use Regine\ValueObjects\DebugInfo;

class Regine
{
    use HasAlternation,
        HasAnchors,
        HasCharacterClasses,
        HasFlags,
        HasGroups,
        HasLiterals,
        HasLookarounds,
        HasQuantifiers,
        HasShorthands;

    protected PatternCollection $components;

    /**
     * Initializes a new Regine instance with an empty pattern component collection.
     */
    public function __construct()
    {
        $this->components = new PatternCollection;
    }

    /**
     * Creates and returns a new Regine instance for building a regex pattern.
     *
     * @return self A new Regine pattern builder instance.
     */
    public static function make(): self
    {
        return new self;
    }

    /**
     * Compiles the regex pattern with the specified delimiter and appends any set flags.
     *
     * @param  string  $delimiter  The delimiter to enclose the pattern (default is '/').
     * @return string The fully compiled regex pattern, including delimiters and flags.
     */
    public function compile(string $delimiter = '/'): string
    {
        $flags = $this->getFlagsString();

        // Auto-enforce Unicode flag if any component requires it.
        $flags = $this->enforeUnicodeAndUpdateFlagsIfRequired();

        return $delimiter . $this->components->compile() . $delimiter . $flags;
    }

    /**
     * Compiles and returns the regex pattern as a raw string without delimiters or flags.
     *
     * Useful for embedding the pattern in contexts where delimiters are not required,
     * such as nested patterns or framework routes.
     *
     * @return string The raw compiled regex pattern.
     */
    public function compileRaw(): string
    {
        return $this->components->compile();
    }

    /**
     * Returns the compiled regex pattern string without delimiters or flags.
     *
     * This method is an alias for {@see compileRaw()}.
     *
     * @return string The raw regex pattern.
     */
    public function raw(): string
    {
        return $this->compileRaw();
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
     * Executes the compiled regex pattern on the given subject and returns all matches.
     *
     * @param  string  $subject  The input string to search for matches.
     * @return array<int|string, string> The array of matches found, including the full match and any capturing groups.
     */
    public function matches(string $subject): array
    {
        $matches = [];
        preg_match($this->compile(), $subject, $matches);

        return $matches;
    }

    /**
     * Returns diagnostic information about the current regex pattern.
     *
     * Provides a DebugInfo object containing the raw pattern,
     * compiled pattern with delimiters and flags, human-readable description,
     * component count, flags string, and metadata for debugging purposes.
     *
     * @return DebugInfo DebugInfo value object containing diagnostic details about the regex pattern.
     */
    public function debug(): DebugInfo
    {
        return new DebugInfo($this);
    }

    /**
     * Enforces the Unicode flag if required.
     *
     * @return string The flags string with the Unicode flag if required.
     */
    protected function enforeUnicodeAndUpdateFlagsIfRequired(): string
    {
        $flags = $this->getFlagsString();
        if (
            // not required or already has the Unicode flag
            ! $this->components->requiresUnicodeFlag() ||
            strpos($flags, 'u') !== false
        ) {
            return $flags;
        }

        // required and missing
        $this->u();

        return $this->getFlagsString(); // return the new flags string
    }
}
