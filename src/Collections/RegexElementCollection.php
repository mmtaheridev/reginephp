<?php

declare(strict_types=1);

namespace Regine\Collections;

use Regine\Contracts\RegexComponent;
use Regine\Contracts\RegexDecorator;
use Regine\Contracts\RegexElement;

/**
 * Collection for managing regex elements (components and decorators) in a pattern.
 *
 * This collection replaces PatternCollection and provides decorator-aware
 * functionality for building complex regex patterns with proper precedence.
 */
class RegexElementCollection
{
    /** @var array<RegexElement> */
    private array $elements = [];

    /**
     * Add a regex element to the collection.
     */
    public function add(RegexElement $element): self
    {
        $this->elements[] = $element;

        return $this;
    }

    /**
     * Add multiple elements to the collection.
     *
     * @param  array<RegexElement>  $elements
     */
    public function addMany(array $elements): self
    {
        foreach ($elements as $element) {
            $this->add($element);
        }

        return $this;
    }

    /**
     * Get all elements in the collection.
     *
     * @return array<RegexElement>
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Get the last element in the collection.
     */
    public function getLastElement(): ?RegexElement
    {
        if (empty($this->elements)) {
            return null;
        }

        $lastIndex = array_key_last($this->elements);

        return $this->elements[$lastIndex];
    }

    /**
     * Check if the collection is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * Get the count of elements.
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * Clear all elements from the collection.
     */
    public function clear(): self
    {
        $this->elements = [];

        return $this;
    }

    /**
     * Remove the last element from the collection.
     */
    public function removeLast(): ?RegexElement
    {
        if (empty($this->elements)) {
            return null;
        }

        return array_pop($this->elements);
    }

    /**
     * Compile all elements into a single regex pattern.
     */
    public function compile(): string
    {
        $pattern = '';
        foreach ($this->elements as $element) {
            $pattern .= $element->compile();
        }

        return $pattern;
    }

    /**
     * Get all components (non-decorator elements) from the collection.
     *
     * @return array<RegexComponent>
     */
    public function getComponents(): array
    {
        $components = [];
        foreach ($this->elements as $element) {
            if ($element instanceof RegexComponent) {
                $components[] = $element;
            } elseif ($element instanceof RegexDecorator) {
                // Extract components from decorated elements recursively
                $components = array_merge($components, $this->extractComponentsFromDecorator($element));
            }
        }

        return $components;
    }

    /**
     * Get all decorators from the collection.
     *
     * @return array<RegexDecorator>
     */
    public function getDecorators(): array
    {
        $decorators = [];
        foreach ($this->elements as $element) {
            if ($element instanceof RegexDecorator) {
                $decorators[] = $element;
                // Recursively get nested decorators
                $decorators = array_merge($decorators, $this->extractDecoratorsFromDecorator($element));
            }
        }

        return $decorators;
    }

    /**
     * Check if the collection contains any decorators.
     */
    public function hasDecorators(): bool
    {
        foreach ($this->elements as $element) {
            if ($element instanceof RegexDecorator) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the collection contains any components.
     */
    public function hasComponents(): bool
    {
        foreach ($this->elements as $element) {
            if ($element instanceof RegexComponent) {
                return true;
            } elseif ($element instanceof RegexDecorator) {
                // Check if decorator contains components
                if (! empty($this->extractComponentsFromDecorator($element))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get metadata for all elements in the collection.
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        $elementMetadata = [];
        foreach ($this->elements as $index => $element) {
            $elementMetadata[$index] = $element->getMetadata();
        }

        return [
            'element_count' => count($this->elements),
            'has_decorators' => $this->hasDecorators(),
            'has_components' => $this->hasComponents(),
            'elements' => $elementMetadata,
            'compiled' => $this->compile(),
        ];
    }

    /**
     * Get a human-readable description of the collection.
     */
    public function getDescription(): string
    {
        if (empty($this->elements)) {
            return 'empty pattern';
        }

        $descriptions = [];
        foreach ($this->elements as $element) {
            $descriptions[] = $element->getDescription();
        }

        return implode(' followed by ', $descriptions);
    }

    /**
     * Check if the collection needs grouping when used in certain contexts.
     * Generally true when there are multiple elements or complex decorators.
     */
    public function needsGroupingForContext(): bool
    {
        // Single element collections don't generally need grouping
        if (count($this->elements) <= 1) {
            return false;
        }

        // Multiple elements typically need grouping for alternation, lookarounds, etc.
        return true;
    }

    /**
     * Compile the collection with contextual grouping applied if needed.
     */
    public function compileWithContextualGrouping(): string
    {
        $pattern = $this->compile();

        if ($this->needsGroupingForContext()) {
            return "(?:{$pattern})";
        }

        return $pattern;
    }

    /**
     * Filter elements by type.
     *
     * @return array<RegexElement>
     */
    public function filterByType(string $type): array
    {
        return array_filter($this->elements, fn ($element) => $element->getType() === $type);
    }

    /**
     * Check if any element requires Unicode mode.
     */
    public function requiresUnicodeFlag(): bool
    {
        foreach ($this->elements as $element) {
            $metadata = $element->getMetadata();
            if (($metadata['requiresUnicode'] ?? false) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract components from a decorator recursively.
     *
     * @return array<RegexComponent>
     */
    private function extractComponentsFromDecorator(RegexDecorator $decorator): array
    {
        $components = [];
        $wrappedElement = $decorator->getWrappedElement();

        if ($wrappedElement instanceof RegexComponent) {
            $components[] = $wrappedElement;
        } elseif ($wrappedElement instanceof RegexDecorator) {
            $components = array_merge($components, $this->extractComponentsFromDecorator($wrappedElement));
        }

        return $components;
    }

    /**
     * Extract decorators from a decorator recursively.
     *
     * @return array<RegexDecorator>
     */
    private function extractDecoratorsFromDecorator(RegexDecorator $decorator): array
    {
        $decorators = [];
        $wrappedElement = $decorator->getWrappedElement();

        if ($wrappedElement instanceof RegexDecorator) {
            $decorators[] = $wrappedElement;
            $decorators = array_merge($decorators, $this->extractDecoratorsFromDecorator($wrappedElement));
        }

        return $decorators;
    }
}
