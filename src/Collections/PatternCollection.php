<?php

declare(strict_types=1);

namespace Regine\Collections;

use Regine\Contracts\RegexComponent;

/**
 * Collection of RegexComponent instances
 *
 * A Regine instance composes zero or more RegexComponent items via this collection.
 */
class PatternCollection
{
    /** @var array<RegexComponent> */
    private array $components = [];

    /**
     * Adds a regex component to the collection.
     *
     * Enables method chaining by returning the current PatternCollection instance.
     *
     * @param  RegexComponent  $component  The regex component to add.
     * @return static The current PatternCollection instance.
     */
    public function add(RegexComponent $component): static
    {
        $this->components[] = $component;

        return $this;
    }

    /**
     * Compiles all regex components in the collection into a single regex pattern string.
     *
     * @return string The concatenated regex pattern from all components.
     */
    public function compile(): string
    {
        $pattern = '';
        foreach ($this->components as $component) {
            $pattern .= $component->compile();
        }

        return $pattern;
    }

    /**
     * Returns the last added RegexComponent in the collection, or null if the collection is empty.
     *
     * @return RegexComponent|null The last RegexComponent instance, or null if none exist.
     */
    public function getLastComponent(): ?RegexComponent
    {
        if ($this->components === []) {
            return null;
        }
        /** @var int $lastIndex */
        $lastIndex = array_key_last($this->components);

        return $this->components[$lastIndex];
    }

    /**
     * Get all components for introspection
     *
     * @return array<RegexComponent>
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * Get count of components
     */
    public function count(): int
    {
        return count($this->components);
    }

    /**
     * Determines whether the collection contains no components.
     *
     * @return bool True if the collection is empty, false otherwise.
     */
    public function isEmpty(): bool
    {
        return $this->components === [];
    }

    /**
     * Get a human-readable description of the pattern
     */
    public function describe(): string
    {
        $descriptions = [];
        foreach ($this->components as $component) {
            $descriptions[] = $component->getDescription();
        }

        return implode(' then ', $descriptions);
    }

    /**
     * Get metadata about all components
     *
     * @return array<array<string, mixed>>
     */
    public function getMetadata(): array
    {
        $metadata = [];
        foreach ($this->components as $index => $component) {
            $metadata[$index] = $component->getMetadata();
        }

        return $metadata;
    }

    /**
     * Determines whether any component signals that Unicode mode is required.
     *
     * Components may expose a 'requiresUnicode' boolean in their metadata.
     */
    public function requiresUnicodeFlag(): bool
    {
        foreach ($this->components as $component) {
            $meta = $component->getMetadata();
            if (($meta['requiresUnicode'] ?? false) === true) {
                return true;
            }
        }

        return false;
    }
}
