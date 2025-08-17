<?php

declare(strict_types=1);

namespace Regine\Composables;

use InvalidArgumentException;
use Regine\Decorators\QuantifierDecorator;
use Regine\Enums\QuantifierTypesEnum;
use Regine\Exceptions\Quantifier\QuantifierForNoPreceedingElementException;
use Regine\Exceptions\Quantifier\UnquatifyablePreceedingElement;

trait HasQuantifiers
{
    /**
     * Match zero or more occurrences of the preceding element
     */
    public function zeroOrMore(): self
    {
        $this->applyQuantifierDecorator(QuantifierTypesEnum::ZERO_OR_MORE);

        return $this;
    }

    /**
     * Match one or more occurrences of the preceding element
     */
    public function oneOrMore(): self
    {
        $this->applyQuantifierDecorator(QuantifierTypesEnum::ONE_OR_MORE);

        return $this;
    }

    /**
     * Match zero or one occurrence of the preceding element
     */
    public function optional(): self
    {
        $this->applyQuantifierDecorator(QuantifierTypesEnum::OPTIONAL);

        return $this;
    }

    /**
     * Match exactly n occurrences of the preceding element
     */
    public function exactly(int $n): self
    {
        $this->applyQuantifierDecorator(QuantifierTypesEnum::EXACTLY, ['count' => $n]);

        return $this;
    }

    /**
     * Match at least n occurrences of the preceding element
     */
    public function atLeast(int $n): self
    {
        $this->applyQuantifierDecorator(QuantifierTypesEnum::AT_LEAST, ['min' => $n]);

        return $this;
    }

    /**
     * Match between min and max occurrences of the preceding element
     */
    public function between(int $min, int $max): self
    {
        $this->applyQuantifierDecorator(QuantifierTypesEnum::BETWEEN, ['min' => $min, 'max' => $max]);

        return $this;
    }

    /**
     * Make the last quantifier lazy (non-greedy)
     */
    public function lazy(): self
    {
        $this->modifyLastQuantifier('lazy');

        return $this;
    }

    /**
     * Make the last quantifier possessive
     */
    public function possessive(): self
    {
        $this->modifyLastQuantifier('possessive');

        return $this;
    }

    /**
     * Apply a quantifier decorator to the last element in the collection.
     *
     * This method uses the decorator pattern to wrap the last element with a quantifier,
     * eliminating the need for complex grouping logic.
     *
     * @param  array<string, int>  $parameters  Parameters for the quantifier (count, min, max)
     *
     * @throws QuantifierForNoPreceedingElementException If there is no preceding element.
     * @throws UnquatifyablePreceedingElement If the last element cannot be quantified.
     */
    private function applyQuantifierDecorator(
        QuantifierTypesEnum $quantifierType,
        array $parameters = []
    ): void {
        $lastElement = $this->elements->getLastElement();

        if ($lastElement === null) {
            throw new QuantifierForNoPreceedingElementException;
        }

        if (! $lastElement->canBeQuantified()) {
            throw new UnquatifyablePreceedingElement;
        }

        // Remove the last element and replace it with a quantified version
        $this->elements->removeLast();

        // Create the quantifier decorator wrapping the element
        $quantifiedElement = new QuantifierDecorator(
            $lastElement,
            $quantifierType,
            $parameters
        );

        // Add the decorated element back to the collection
        $this->elements->add($quantifiedElement);
    }

    /**
     * Modify the last quantifier to be lazy or possessive.
     *
     * @throws InvalidArgumentException If the last element is not a quantifier decorator
     */
    private function modifyLastQuantifier(string $modifier): void
    {
        $lastElement = $this->elements->getLastElement();

        if (! $lastElement instanceof QuantifierDecorator) {
            throw new InvalidArgumentException("Cannot apply {$modifier} modifier: last element is not a quantifier");
        }

        // Remove the last element and replace it with a modified version
        $this->elements->removeLast();

        $modifiedQuantifier = match ($modifier) {
            'lazy' => $lastElement->makeLazy(),
            'possessive' => $lastElement->makePossessive(),
            default => throw new InvalidArgumentException("Unknown quantifier modifier: {$modifier}")
        };

        $this->elements->add($modifiedQuantifier);
    }
}
