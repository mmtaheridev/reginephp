<?php

declare(strict_types=1);

namespace Regine\Collections;

use ReflectionClass;
use Regine\Contracts\RegexDecorator;
use Regine\Contracts\RegexElement;
use Regine\Enums\DecoratorType;

/**
 * Manages the application of decorators in the correct precedence order.
 *
 * Ensures decorators are applied in the proper order: Component → Quantifier → Group → Lookaround
 * This maintains consistent regex compilation regardless of the order decorators are added.
 */
class DecoratorChain
{
    /** @var array<RegexDecorator> */
    private array $decorators = [];

    private RegexElement $coreElement;

    public function __construct(RegexElement $coreElement)
    {
        $this->coreElement = $coreElement;
    }

    /**
     * Add a decorator to the chain.
     * Decorators will be automatically sorted by precedence when compiled.
     */
    public function addDecorator(RegexDecorator $decorator): self
    {
        $this->decorators[] = $decorator;

        return $this;
    }

    /**
     * Get all decorators in the chain.
     *
     * @return array<RegexDecorator>
     */
    public function getDecorators(): array
    {
        return $this->decorators;
    }

    /**
     * Get the core element (the innermost element).
     */
    public function getCoreElement(): RegexElement
    {
        return $this->coreElement;
    }

    /**
     * Check if the chain has any decorators.
     */
    public function hasDecorators(): bool
    {
        return count($this->decorators) > 0;
    }

    /**
     * Get the count of decorators in the chain.
     */
    public function getDecoratorCount(): int
    {
        return count($this->decorators);
    }

    /**
     * Check if the chain has a specific type of decorator.
     */
    public function hasDecoratorType(DecoratorType $type): bool
    {
        foreach ($this->decorators as $decorator) {
            if ($decorator->getPrecedence() === $type->getPrecedence()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get decorators of a specific type.
     *
     * @return array<RegexDecorator>
     */
    public function getDecoratorsOfType(DecoratorType $type): array
    {
        $result = [];
        foreach ($this->decorators as $decorator) {
            if ($decorator->getPrecedence() === $type->getPrecedence()) {
                $result[] = $decorator;
            }
        }

        return $result;
    }

    /**
     * Apply all decorators to the core element in the correct precedence order.
     * Returns the final decorated element.
     */
    public function compile(): RegexElement
    {
        if (empty($this->decorators)) {
            return $this->coreElement;
        }

        // Sort decorators by precedence (lower numbers first)
        $sortedDecorators = $this->getSortedDecorators();

        // Apply decorators in order, with each decorator wrapping the previous result
        $currentElement = $this->coreElement;

        foreach ($sortedDecorators as $decorator) {
            // Create a new instance of the decorator wrapping the current element
            $currentElement = $this->applyDecorator($decorator, $currentElement);
        }

        return $currentElement;
    }

    /**
     * Get the compiled regex string with all decorators applied.
     */
    public function compileToString(): string
    {
        return $this->compile()->compile();
    }

    /**
     * Get metadata for the entire decorator chain.
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return [
            'core_element' => $this->coreElement->getMetadata(),
            'decorator_count' => count($this->decorators),
            'decorators' => array_map(fn ($d) => $d->getMetadata(), $this->decorators),
            'precedence_order' => array_map(fn ($d) => $d->getPrecedence(), $this->getSortedDecorators()),
        ];
    }

    /**
     * Get a human-readable description of the decorator chain.
     */
    public function getDescription(): string
    {
        if (empty($this->decorators)) {
            return $this->coreElement->getDescription();
        }

        $descriptions = [$this->coreElement->getDescription()];

        foreach ($this->getSortedDecorators() as $decorator) {
            $descriptions[] = $decorator->getDescription();
        }

        return implode(' → ', $descriptions);
    }

    /**
     * Get decorators sorted by precedence (lower precedence numbers first).
     *
     * @return array<RegexDecorator>
     */
    private function getSortedDecorators(): array
    {
        $decorators = $this->decorators;

        usort($decorators, function (RegexDecorator $a, RegexDecorator $b) {
            return $a->getPrecedence() <=> $b->getPrecedence();
        });

        return $decorators;
    }

    /**
     * Apply a decorator to an element, creating a new decorated element.
     * This method recreates the decorator with the new wrapped element.
     */
    private function applyDecorator(RegexDecorator $decorator, RegexElement $element): RegexElement
    {
        // Get the decorator's class and create a new instance wrapping the element
        $decoratorClass = get_class($decorator);

        // Use reflection to recreate the decorator with the new element
        $reflection = new ReflectionClass($decoratorClass);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            // Fallback: decorator doesn't have a constructor (shouldn't happen)
            return $decorator;
        }

        // Get constructor parameters and create new instance
        $parameters = $constructor->getParameters();
        $args = [$element]; // First parameter is always the element

        // Copy other constructor arguments from the original decorator
        // This is a simplified approach - in a real implementation, we might need
        // to store constructor arguments in the decorator for recreation

        return $reflection->newInstanceArgs($args);
    }
}
