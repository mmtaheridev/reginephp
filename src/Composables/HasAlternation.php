<?php

declare(strict_types=1);

namespace Regine\Composables;

use InvalidArgumentException;
use Regine\Collections\PatternCollection;
use Regine\Components\AlternationComponent;
use Regine\Components\GroupComponent;
use Regine\Enums\GroupTypesEnum;
use Regine\Regine;
use RuntimeException;

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
            throw new InvalidArgumentException('Alternation callback must return a Regine instance.');
        }

        // Wrap the alternation pattern in a non-capturing group
        $component = new GroupComponent(
            GroupTypesEnum::NON_CAPTURING,
            $alternationPattern
        );

        $this->components->add($component);

        return $this;
    }

    /**
     * Add alternation with a single alternative within the current scope
     *
     * This method works by taking the last component and creating alternation with it.
     * If the last component is already an alternation, it adds to that alternation.
     * Otherwise, it creates a new alternation component.
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
        $lastComponent = $this->components->getLastComponent();

        if ($lastComponent === null) {
            // No previous components, just add the alternative
            $component = AlternationComponent::single($alternative);
            $this->components->add($component);

            return $this;
        }

        // If the last component is already an alternation, extend it
        if ($lastComponent->getType() === 'alternation') {
            // Remove the last alternation component
            $components = $this->components->getComponents();
            $alternationComponent = array_pop($components);

            if ($alternationComponent === null) {
                throw new RuntimeException('Expected alternation component but found null');
            }

            // Get existing alternatives and add the new one
            $metadata = $alternationComponent->getMetadata();
            /** @var array<Regine|string> $existingAlternatives */
            $existingAlternatives = $metadata['alternatives'] ?? [];
            $newAlternative = $alternative instanceof Regine ?
                $this->compilePattern($alternative) :
                $alternative;
            $existingAlternatives[] = $newAlternative;

            // Create new alternation with all alternatives
            $newAlternationComponent = AlternationComponent::multiple($existingAlternatives);

            // Rebuild components collection
            $this->components = new PatternCollection;
            foreach ($components as $component) {
                $this->components->add($component);
            }
            $this->components->add($newAlternationComponent);

            return $this;
        }

        // Create alternation between the last component and the new alternative
        $lastComponentPattern = $lastComponent->compile();
        $alternativePattern = $alternative instanceof Regine ?
            $this->compilePattern($alternative) :
            $alternative;

        // Remove the last component
        $components = $this->components->getComponents();
        array_pop($components);

        // Create alternation
        $alternationComponent = AlternationComponent::multiple([$lastComponentPattern, $alternativePattern]);

        // Rebuild components collection
        $this->components = new PatternCollection;
        foreach ($components as $component) {
            $this->components->add($component);
        }
        $this->components->add($alternationComponent);

        return $this;
    }

    /**
     * Add alternation with multiple alternatives within the current scope
     *
     * <code>
     *      $regine = Regine::make()->orAny(['http', 'https', 'ftp']); // http|https|ftp
     *      $regine = Regine::make()->orAny([
     *          Regine::make()->literal('cat'),
     *          Regine::make()->literal('dog'),
     *          'bird'
     *      ]); // cat|dog|bird
     * </code>
     *
     * @param  array<Regine|string>  $alternatives  The alternative patterns
     */
    public function orAny(array $alternatives): self
    {
        $component = AlternationComponent::multiple($alternatives);
        $this->components->add($component);

        return $this;
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
