<?php

declare(strict_types=1);

namespace Regine\Components;

use Regine\Contracts\RegexComponent;
use Regine\Enums\QuantifierTypesEnum;
use Regine\Exceptions\Quantifier\MinIsBiggerThanMaxException;
use Regine\Exceptions\Quantifier\NegativeQuantifierException;

class QuantifierComponent implements RegexComponent
{
    private QuantifierTypesEnum $quantifierType;
    /** @var array<string, int> */
    private array $parameters;

    /**
     * @param  array<string, int>  $parameters
     */
    public function __construct(QuantifierTypesEnum $quantifierType, array $parameters = [])
    {
        $this->quantifierType = $quantifierType;
        $this->parameters = $parameters;
    }

    /**
     * Creates a quantifier that matches zero or more occurrences of the preceding element.
     */
    public static function zeroOrMore(): self
    {
        return new self(QuantifierTypesEnum::ZERO_OR_MORE);
    }

    /**
     * Creates a quantifier that matches one or more occurrences of the preceding element.
     */
    public static function oneOrMore(): self
    {
        return new self(QuantifierTypesEnum::ONE_OR_MORE);
    }

    /**
     * Creates a quantifier that matches zero or one occurrence of the preceding element.
     */
    public static function optional(): self
    {
        return new self(QuantifierTypesEnum::OPTIONAL);
    }

    /**
     * Creates a quantifier that matches exactly n occurrences of the preceding element.
     *
     * @throws NegativeQuantifierException If the count is negative.
     */
    public static function exactly(int $count): self
    {
        if ($count < 0) {
            throw new NegativeQuantifierException;
        }

        return new self(QuantifierTypesEnum::EXACTLY, ['count' => $count]);
    }

    /**
     * @throws NegativeQuantifierException If the minimum count is negative.
     */
    public static function atLeast(int $count): self
    {
        if ($count < 0) {
            throw new NegativeQuantifierException;
        }

        return new self(QuantifierTypesEnum::AT_LEAST, ['min' => $count]);
    }

    /**
     * @throws NegativeQuantifierException If the minimum or maximum count is negative.
     * @throws MinIsBiggerThanMaxException If the minimum count is greater than the maximum count.
     */
    public static function between(int $min, int $max): self
    {
        if ($min < 0 || $max < 0) {
            throw new NegativeQuantifierException;
        }

        if ($min > $max) {
            throw new MinIsBiggerThanMaxException(min: $min, max: $max);
        }

        return new self(QuantifierTypesEnum::BETWEEN, ['min' => $min, 'max' => $max]);
    }

    /**
     * Compiles the quantifier component into its corresponding regex string representation based on the quantifier type.
     *
     * @return string The compiled regex string for the quantifier.
     */
    public function compile(): string
    {
        return $this->quantifierType->getRegex($this->parameters);
    }

    /**
     * Returns the type identifier for this regex component.
     *
     * @return string The string 'QUANTIFIER'.
     */
    public function getType(): string
    {
        return $this->quantifierType->value;
    }

    /**
     * Returns an associative array containing metadata about the quantifier component, including its type, quantifier, parameters, and enum value.
     *
     * @return array{type: string, quantifier: string, parameters: array<string, int>, enum: string}
     */
    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'quantifier' => $this->quantifierType->getRegex($this->parameters),
            'parameters' => $this->parameters,
            'enum' => $this->quantifierType->name,
        ];
    }

    /**
     * Indicates whether the component can be quantified.
     *
     * @return false Always returns false.
     */
    public function canBeQuantified(): false
    {
        return false; // Quantifiers cannot be quantified
    }

    /**
     * Returns a human-readable description of the quantifier component.
     *
     * @return string The description of the quantifier component.
     */
    public function getDescription(): string
    {
        $description = $this->quantifierType->getBaseDescription();

        // Replace placeholders with actual values
        foreach ($this->parameters as $key => $value) {
            $description = str_replace("{{$key}}", (string) $value, $description);
        }

        return $description;
    }
}
