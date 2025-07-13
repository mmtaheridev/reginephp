<?php

declare(strict_types=1);

namespace Regine\Components;

use InvalidArgumentException;
use Regine\Contracts\RegexComponent;
use Regine\Regine;

class AlternationComponent implements RegexComponent
{
    /** @var array<string> */
    private array $alternatives;

    /**
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

    public function compile(): string
    {
        return implode('|', $this->alternatives);
    }

    public function getType(): string
    {
        return 'alternation';
    }

    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'alternatives' => $this->alternatives,
            'count' => count($this->alternatives),
        ];
    }

    public function canBeQuantified(): bool
    {
        return true;
    }

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
