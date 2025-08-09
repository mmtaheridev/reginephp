<?php

declare(strict_types=1);

namespace Regine\Components;

use Regine\Contracts\RegexComponent;
use Regine\Enums\LookaroundTypesEnum;
use Regine\Regine;

class LookaroundComponent implements RegexComponent
{
    private LookaroundTypesEnum $type;
    private string $pattern;

    /**
     * Initializes a LookaroundComponent with the specified lookaround type and pattern.
     *
     * @param  LookaroundTypesEnum  $type  The type of lookaround assertion to use.
     * @param  Regine|string  $pattern  The pattern to be used within the lookaround, either as a Regine object or a string.
     */
    public function __construct(LookaroundTypesEnum $type, Regine|string $pattern)
    {
        $this->type = $type;
        $this->pattern = $this->compilePattern($pattern);
    }

    /**
     * Creates a positive lookahead regex component that asserts the given pattern must follow the current position.
     *
     * @param  Regine|string  $pattern  The pattern to assert in the lookahead.
     * @return self A new LookaroundComponent representing a positive lookahead.
     */
    public static function positiveLookahead(Regine|string $pattern): self
    {
        return new self(LookaroundTypesEnum::POSITIVE_LOOKAHEAD, $pattern);
    }

    /**
     * Creates a negative lookahead regex component.
     *
     * A negative lookahead asserts that the specified pattern does not occur at the current position in the input.
     *
     * @param  Regine|string  $pattern  The pattern to assert does not follow the current position.
     * @return self A new negative lookahead component.
     */
    public static function negativeLookahead(Regine|string $pattern): self
    {
        return new self(LookaroundTypesEnum::NEGATIVE_LOOKAHEAD, $pattern);
    }

    /**
     * Creates a positive lookbehind regex component.
     *
     * A positive lookbehind asserts that the specified pattern must appear immediately before the current position in the input.
     *
     * @param  Regine|string  $pattern  The pattern to assert behind the current position.
     * @return self A new LookaroundComponent representing the positive lookbehind assertion.
     */
    public static function positiveLookbehind(Regine|string $pattern): self
    {
        return new self(LookaroundTypesEnum::POSITIVE_LOOKBEHIND, $pattern);
    }

    /**
     * Creates a regex negative lookbehind component that asserts the specified pattern does not precede the current position.
     *
     * @param  Regine|string  $pattern  The pattern to use in the negative lookbehind assertion.
     * @return self A new LookaroundComponent representing the negative lookbehind.
     */
    public static function negativeLookbehind(Regine|string $pattern): self
    {
        return new self(LookaroundTypesEnum::NEGATIVE_LOOKBEHIND, $pattern);
    }

    /**
     * Returns the regex string representation of the lookaround component.
     *
     * The returned string is formatted according to the lookaround type and the compiled pattern.
     *
     * @return string The compiled regex lookaround pattern.
     */
    public function compile(): string
    {
        return $this->type->getPattern($this->pattern);
    }

    /**
     * Returns the string identifier for this component type.
     *
     * @return string The string 'lookaround'.
     */
    public function getType(): string
    {
        return 'lookaround';
    }

    /**
     * Returns metadata describing the lookaround component.
     *
     * The metadata includes the component type, lookaround type value, pattern string, and enum name.
     *
     * @return array<string, mixed> Associative array with keys: 'type', 'lookaround_type', 'pattern', and 'enum'.
     */
    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'lookaround_type' => $this->type->value,
            'pattern' => $this->pattern,
            'enum' => $this->type->name,
        ];
    }

    /**
     * Indicates that lookaround components cannot be quantified.
     *
     * @return bool Always returns false.
     */
    public function canBeQuantified(): bool
    {
        return false; // Lookarounds cannot be quantified
    }

    /**
     * Returns a human-readable description of the lookaround assertion, including its type and pattern.
     *
     * @return string The description of the lookaround component.
     */
    public function getDescription(): string
    {
        return "{$this->type->getDescription()} assertion for '{$this->pattern}'";
    }

    /**
     * Converts a Regine object or string pattern to its string representation.
     *
     * If the pattern is a Regine object, its raw compiled pattern is returned; otherwise, the string is returned as-is.
     *
     * @param  Regine|string  $pattern  The pattern to compile.
     * @return string The compiled pattern string.
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
