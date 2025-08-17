<?php

declare(strict_types=1);

namespace Regine\Decorators;

use InvalidArgumentException;
use Regine\Abstracts\BaseDecorator;
use Regine\Contracts\RegexElement;
use Regine\Enums\DecoratorType;
use Regine\Enums\QuantifierTypesEnum;
use Regine\Exceptions\Quantifier\MinIsBiggerThanMaxException;
use Regine\Exceptions\Quantifier\NegativeQuantifierException;

/**
 * Decorator for applying quantifiers to regex elements.
 *
 * Handles all quantifier types including simple (+, *, ?), exact counts,
 * ranges, and modifiers (lazy, possessive).
 */
class QuantifierDecorator extends BaseDecorator
{
    private QuantifierTypesEnum $quantifierType;
    /** @var array<string, int> */
    private array $parameters;
    private bool $lazy;
    private bool $possessive;

    /**
     * @param  array<string, int>  $parameters  Quantifier parameters (count, min, max)
     */
    public function __construct(
        RegexElement $element,
        QuantifierTypesEnum $quantifierType,
        array $parameters = [],
        bool $lazy = false,
        bool $possessive = false
    ) {
        parent::__construct($element);
        $this->quantifierType = $quantifierType;
        $this->parameters = $parameters;
        $this->lazy = $lazy;
        $this->possessive = $possessive;

        $this->validateParameters();
    }

    public function compile(): string
    {
        $elementContent = $this->element->compile();

        // Wrap element in non-capturing group if it needs grouping
        if ($this->wrappedElementNeedsGrouping()) {
            $elementContent = "(?:{$elementContent})";
        }

        $quantifier = $this->quantifierType->getRegex($this->parameters);

        // Add lazy or possessive modifiers
        if ($this->lazy) {
            $quantifier .= '?';
        } elseif ($this->possessive) {
            $quantifier .= '+';
        }

        return $elementContent . $quantifier;
    }

    public function getType(): string
    {
        return DecoratorType::QUANTIFIER->value;
    }

    public function canBeQuantified(): bool
    {
        // Quantifiers cannot be quantified again
        return false;
    }

    public function needsGrouping(): bool
    {
        // Quantified elements don't need additional grouping
        return false;
    }

    public function getMetadata(): array
    {
        $metadata = parent::getMetadata();
        $metadata['quantifier_type'] = $this->quantifierType->value;
        $metadata['quantifier_regex'] = $this->quantifierType->getRegex($this->parameters);
        $metadata['parameters'] = $this->parameters;
        $metadata['lazy'] = $this->lazy;
        $metadata['possessive'] = $this->possessive;
        $metadata['description'] = $this->getQuantifierDescription();

        return $metadata;
    }

    /**
     * Get the quantifier type.
     */
    public function getQuantifierType(): QuantifierTypesEnum
    {
        return $this->quantifierType;
    }

    /**
     * Get the quantifier parameters.
     *
     * @return array<string, int>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Check if this quantifier is lazy (non-greedy).
     */
    public function isLazy(): bool
    {
        return $this->lazy;
    }

    /**
     * Check if this quantifier is possessive.
     */
    public function isPossessive(): bool
    {
        return $this->possessive;
    }

    /**
     * Create a lazy version of this quantifier.
     */
    public function makeLazy(): self
    {
        return new self(
            element: $this->element,
            quantifierType: $this->quantifierType,
            parameters: $this->parameters,
            lazy: true,
            possessive: false // Can't be both lazy and possessive
        );
    }

    /**
     * Create a possessive version of this quantifier.
     */
    public function makePossessive(): self
    {
        return new self(
            element: $this->element,
            quantifierType: $this->quantifierType,
            parameters: $this->parameters,
            lazy: false, // Can't be both lazy and possessive
            possessive: true
        );
    }

    protected function getDecoratorType(): DecoratorType
    {
        return DecoratorType::QUANTIFIER;
    }

    protected function getDecoratorDescription(): string
    {
        $description = $this->getQuantifierDescription();

        if ($this->lazy) {
            $description .= ' (lazy)';
        } elseif ($this->possessive) {
            $description .= ' (possessive)';
        }

        return $description;
    }

    private function validateParameters(): void
    {
        $this->validateCounts();
        $this->validateModifiers();
    }

    private function validateCounts(): void
    {
        foreach (['count', 'min', 'max'] as $param) {
            if (isset($this->parameters[$param]) && $this->parameters[$param] < 0) {
                throw new NegativeQuantifierException;
            }
        }

        if ($this->quantifierType === QuantifierTypesEnum::BETWEEN) {
            $min = (int) ($this->parameters['min'] ?? 0);
            $max = (int) ($this->parameters['max'] ?? 0);

            if ($min > $max) {
                throw new MinIsBiggerThanMaxException($min, $max);
            }
        }
    }

    private function validateModifiers(): void
    {
        // Cannot be both lazy and possessive
        if ($this->lazy && $this->possessive) {
            throw new InvalidArgumentException('Quantifier cannot be both lazy and possessive');
        }
    }

    private function getQuantifierDescription(): string
    {
        $baseDescription = $this->quantifierType->getBaseDescription();

        // Replace placeholders with actual values
        foreach ($this->parameters as $key => $value) {
            $baseDescription = str_replace("{{$key}}", (string) ((int) $value), $baseDescription);
        }

        return $baseDescription;
    }
}
