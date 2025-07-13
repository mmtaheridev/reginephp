<?php

declare(strict_types=1);

namespace Regine\Collections;

use Regine\Contracts\RegexComponent;

class PatternCollection
{
    /** @var array<RegexComponent> */
    private array $components = [];

    public function add(RegexComponent $component): self
    {
        $this->components[] = $component;

        return $this;
    }

    public function compile(): string
    {
        $pattern = '';
        foreach ($this->components as $component) {
            $pattern .= $component->compile();
        }

        return $pattern;
    }

    /**
     * Get the last component added (for quantification)
     */
    public function getLastComponent(): ?RegexComponent
    {
        if (empty($this->components)) {
            return null;
        }

        return end($this->components);
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
     * Check if collection is empty
     */
    public function isEmpty(): bool
    {
        return empty($this->components);
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
}
