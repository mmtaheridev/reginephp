<?php

declare(strict_types=1);

namespace Regine\Components;

use Regine\Contracts\RegexComponent;
use Regine\Enums\ComponentType;
use Regine\Exceptions\Alternation\EmptyAlternationException;

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
     * Initializes an alternation component with one or more alternatives.
     *
     * @param  array<string>  $alternatives  An array of alternative patterns as strings.
     *
     * @throws EmptyAlternationException If no alternatives are provided.
     */
    public function __construct(array $alternatives)
    {
        if ($alternatives === []) {
            throw new EmptyAlternationException;
        }

        $this->alternatives = $alternatives;
    }

    /**
     * Creates an alternation component with a single alternative.
     *
     * @param  string  $alternative  The alternative pattern to include in the alternation.
     * @return self The created AlternationComponent instance.
     */
    public static function single(string $alternative): self
    {
        return new self([$alternative]);
    }

    /**
     * Creates an alternation component from multiple alternatives.
     *
     * @param  array<string>  $alternatives  The alternative patterns to include in the alternation.
     * @return self The constructed alternation component.
     */
    public static function multiple(array $alternatives): self
    {
        return new self($alternatives);
    }

    /**
     * Compiles the alternation component into a regex pattern string by joining all alternatives with the pipe (`|`) character.
     *
     * @return string The compiled regex alternation pattern.
     */
    public function compile(): string
    {
        return implode('|', $this->alternatives);
    }

    /**
     * Returns the type identifier for this component.
     *
     * @return string The string 'alternation'.
     */
    public function getType(): string
    {
        return ComponentType::ALTERNATION->value;
    }

    /**
     * Returns metadata describing the alternation component.
     *
     * The metadata includes the component type, the list of compiled alternatives, and the total number of alternatives.
     *
     * @return array<string, mixed> Associative array with keys 'type', 'alternatives', and 'count'.
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
     * Indicates that the alternation component supports quantifiers.
     *
     * @return bool Always returns true.
     */
    public function canBeQuantified(): bool
    {
        return true;
    }

    /**
     * Returns a human-readable description of the alternation, listing all alternatives and their count.
     *
     * @return string A description indicating the alternatives and their total number.
     */
    public function getDescription(): string
    {
        $count = count($this->alternatives);
        $alternatives = implode("', '", $this->alternatives);

        return "match any of '{$alternatives}' ({$count} alternatives)";
    }
}
