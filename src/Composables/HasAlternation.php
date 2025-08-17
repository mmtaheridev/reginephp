<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\AlternationComponent;
use Regine\Components\RawPatternComponent;
use Regine\Decorators\GroupDecorator;
use Regine\Enums\GroupTypesEnum;
use Regine\Exceptions\Alternation\AlternationCallbackDoesntReturnRegine;
use Regine\Regine;

/**
 * Trait for adding alternation methods to Regine
 *
 * This trait provides methods for creating alternation patterns
 * (OR) between multiple alternatives
 */
trait HasAlternation
{
    /**
     * Create a scoped alternation using a closure
     *
     * The closure receives a new Regine instance and should return a Regine instance
     * with alternation patterns built using or() and orAny() methods.
     * The result will be automatically wrapped in a non-capturing group.
     *
     * <code>
     *      $regine = Regine::make()
     *          ->literal('prefix')
     *          ->alternation(function() {
     *              return Regine::make()
     *                  ->literal('http')
     *                  ->or('https')
     *                  ->or('ftp');
     *          })
     *          ->literal('://'); // prefix(http|https|ftp)://
     * </code>
     *
     * @param  callable(): Regine  $callback  Function that returns a Regine instance with alternation
     */
    public function alternation(callable $callback): self
    {
        $alternationPattern = $callback();

        if (! $alternationPattern instanceof Regine) {
            throw new AlternationCallbackDoesntReturnRegine;
        }

        // Convert the alternation pattern to an element and wrap in a non-capturing group
        $element = new RawPatternComponent($alternationPattern->compileRaw());
        $groupDecorator = new GroupDecorator(
            element: $element,
            groupType: GroupTypesEnum::NON_CAPTURING
        );

        $this->elements->add($groupDecorator);

        return $this;
    }

    /**
     * Add alternation with a single alternative within the current scope
     *
     * This method creates alternation with the last element. The decorator pattern
     * handles grouping automatically when needed.
     *
     * <code>
     *      $regine = Regine::make()->literal('http')->or('https'); // http|https
     *      $regine = Regine::make()->literal('cat')->or('dog')->or('bird'); // cat|dog|bird
     * </code>
     *
     * @param  Regine|string  $alternative  The alternative pattern
     */
    public function or(Regine|string $alternative): self
    {
        $lastElement = $this->elements->getLastElement();
        $alternativePattern = $this->compilePattern($alternative);

        if ($lastElement === null) {
            // No previous elements, just add the alternative as a single alternation
            $alternation = AlternationComponent::single($alternativePattern);
            $this->elements->add($alternation);

            return $this;
        }

        // If the last element is already an alternation, extend it
        if ($lastElement->getType() === 'alternation') {
            $this->elements->removeLast();

            $metadata = $lastElement->getMetadata();
            /** @var array<string> $existingAlternatives */
            $existingAlternatives = $metadata['alternatives'] ?? [];
            $existingAlternatives[] = $alternativePattern;

            $newAlternation = AlternationComponent::multiple($existingAlternatives);
            $this->elements->add($newAlternation);

            return $this;
        }

        // Create alternation between the last element and the new alternative
        $this->elements->removeLast();
        $lastElementPattern = $lastElement->compile();

        $alternation = AlternationComponent::multiple([$lastElementPattern, $alternativePattern]);
        $this->elements->add($alternation);

        return $this;
    }

    /**
     * Add alternation with multiple alternatives within the current scope
     *
     * This creates an alternation where exactly one of the provided alternatives can match.
     * The decorator pattern handles proper grouping automatically when needed.
     *
     * <code>
     *      $regine = Regine::make()->oneOf(['http', 'https', 'ftp']); // http|https|ftp
     *      $regine = Regine::make()->literal('start')->oneOf(['middle', 'center'])->literal('end'); // start(middle|center)end
     * </code>
     *
     * @param  array<Regine|string>  $alternatives  The alternative patterns (strings or Regine objects)
     */
    public function oneOf(array $alternatives): self
    {
        // Convert any Regine objects to strings
        $compiledAlternatives = array_map(
            fn ($alternative) => $this->compilePattern($alternative),
            $alternatives
        );

        $alternation = AlternationComponent::multiple($compiledAlternatives);
        $this->elements->add($alternation);

        return $this;
    }

    /**
     * Add alternation with multiple alternatives within the current scope
     *
     * @deprecated Use oneOf() instead for clearer intent. This method will be removed in a future version.
     *
     * @param  array<string>  $alternatives  The alternative patterns (simplified to strings only)
     */
    public function orAny(array $alternatives): self
    {
        return $this->oneOf($alternatives);
    }

    /**
     * Compile a pattern (Regine object or string) to its string representation
     */
    private function compilePattern(Regine|string $pattern): string
    {
        if ($pattern instanceof Regine) {
            // Use the raw compilation method for nesting patterns
            return $pattern->compileRaw();
        }

        return $pattern;
    }
}
