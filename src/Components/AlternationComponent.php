<?php

declare(strict_types=1);

namespace Regine\Components;

use InvalidArgumentException;
use Regine\Contracts\RegexComponent;
use Regine\Regine;

/**
 * Alternation component
 * 
 * Implements a regex component that represents an alternation (OR)
 * between multiple alternatives
 */
class AlternationComponent implements RegexComponent
{
    /** @var array<string> */
    private array $alternatives;

    /**
     * Create an alternation component
     *
     * @param  array<Regine|string>  $alternatives
     */
    public function __construct(array $alternatives)
    {
        if ($alternatives === []) {
            throw new InvalidArgumentException('Alternation requires at least one alternative.');
        }

        $this->alternatives = array_map(
            fn (Regine|string $alternative) => $this->compilePattern($alternative),
            $alternatives
        );
    }

    /**
     * Create alternation with single alternative
     */
    public static function single(Regine|string $alternative): self
    {
        return new self([$alternative]);
    }

    /**
     * Create alternation with multiple alternatives
     *
     * @param  array<Regine|string>  $alternatives
     */
    public static function multiple(array $alternatives): self
    {
        return new self($alternatives);
    }

    /**
     * Compile the alternation component into a regex string
     */
    public function compile(): string
    {
        return implode('|', $this->alternatives);
    }

    /**
     * Get the type of the alternation component
     */
    public function getType(): string
    {
        return 'alternation';
    }

    /**
     * Get metadata about the alternation component
     *
     * @return array<type: string, alternatives: array<string>, count: int>
     */
    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'alternatives' => $this->alternatives,
            'count' => count($this->alternatives),
        ];
    }

    /**
     * Check if the alternation component can be quantified
     */
    public function canBeQuantified(): bool
    {
        return true;
    }

    /**
     * Get a human-readable description of the alternation component
     */
    public function getDescription(): string
    {
        $count = count($this->alternatives);
        $alternatives = implode("', '", $this->alternatives);

        return "match any of '{$alternatives}' ({$count} alternatives)";
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
