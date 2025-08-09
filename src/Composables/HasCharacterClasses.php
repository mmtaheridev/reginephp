<?php

declare(strict_types=1);

namespace Regine\Composables;

use InvalidArgumentException;
use Regine\Components\CharacterClassComponent;

trait HasCharacterClasses
{
    /**
     * Adds a character class that matches any of the provided characters.
     *
     * @param  string  $chars  The characters to include inside the class, e.g. "abc" → `[abc]`.
     * @return self Fluent builder instance for chaining.
     * @throws InvalidArgumentException If $chars is empty.
     */
    public function anyOf(string $chars): self
    {
        $this->components->add(CharacterClassComponent::anyOf($chars));

        return $this;
    }

    /**
     * Adds a negated character class that matches none of the provided characters.
     *
     * @param  string  $chars  The characters to exclude inside the class, e.g. "abc" → `[^abc]`.
     * @return self Fluent builder instance for chaining.
     * @throws InvalidArgumentException If $chars is empty.
     */
    public function noneOf(string $chars): self
    {
        $this->components->add(CharacterClassComponent::noneOf($chars));

        return $this;
    }

    /**
     * Adds a character range from one character to another (inclusive).
     *
     * @param  string  $from  The starting character of the range (single UTF-8 character).
     * @param  string  $to  The ending character of the range (single UTF-8 character).
     * @return self Fluent builder instance for chaining.
     *
     * @throws InvalidArgumentException If boundaries are not single characters or start > end or if $from or $to is empty.
     */
    public function range(string $from, string $to): self
    {
        $this->components->add(CharacterClassComponent::range($from, $to));

        return $this;
    }

    /**
     * Adds a negated character range from one character to another (inclusive).
     *
     * Produces a pattern like `[^a-z]`.
     *
     * @param  string  $from  The starting character of the range (single UTF-8 character).
     * @param  string  $to  The ending character of the range (single UTF-8 character).
     * @return self Fluent builder instance for chaining.
     *
     * @throws InvalidArgumentException If boundaries are not single characters or start > end.
     *
     * <code>
     * $pattern = Regine::make()
     *     ->noneOfRange('0', '9')  // anything except digits
     *     ->oneOrMore()
     *     ->compile(); // '/[^0-9]+/'
     * </code>
     */
    public function noneOfRange(string $from, string $to): self
    {
        $this->components->add(CharacterClassComponent::noneOfRange($from, $to));

        return $this;
    }
}
