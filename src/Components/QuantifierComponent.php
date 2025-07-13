<?php

declare(strict_types=1);

namespace Regine\Components;

use InvalidArgumentException;
use Regine\Contracts\RegexComponent;
use Regine\Enums\QuantifierTypesEnum;

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

    public static function zeroOrMore(): self
    {
        return new self(QuantifierTypesEnum::ZERO_OR_MORE);
    }

    public static function oneOrMore(): self
    {
        return new self(QuantifierTypesEnum::ONE_OR_MORE);
    }

    public static function optional(): self
    {
        return new self(QuantifierTypesEnum::OPTIONAL);
    }

    public static function exactly(int $n): self
    {
        if ($n < 0) {
            throw new InvalidArgumentException('Quantifier count must be non-negative.');
        }

        return new self(QuantifierTypesEnum::EXACTLY, ['count' => $n]);
    }

    public static function atLeast(int $n): self
    {
        if ($n < 0) {
            throw new InvalidArgumentException('Quantifier count must be non-negative.');
        }

        return new self(QuantifierTypesEnum::AT_LEAST, ['min' => $n]);
    }

    public static function between(int $min, int $max): self
    {
        if ($min < 0 || $max < 0) {
            throw new InvalidArgumentException('Quantifier counts must be non-negative.');
        }

        if ($min > $max) {
            throw new InvalidArgumentException('Minimum count must be less than or equal to maximum count.');
        }

        return new self(QuantifierTypesEnum::BETWEEN, ['min' => $min, 'max' => $max]);
    }

    public function compile(): string
    {
        return $this->quantifierType->getRegex($this->parameters);
    }

    public function getType(): string
    {
        return $this->quantifierType->value;
    }

    /**
     * @return array<string, mixed>
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

    public function canBeQuantified(): bool
    {
        return false; // Quantifiers cannot be quantified
    }

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
