<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\CharacterClassComponent;
use Regine\Exceptions\CharacterClass\EmptyCharacterClassException;
use Regine\Exceptions\CharacterClass\RangeBoundariesAreNotSetException;
use Regine\Exceptions\CharacterClass\RangeBoundariesNotSingleCharacterException;
use Regine\Exceptions\CharacterClass\RangeBoundariesNotValidUTF8Exception;
use Regine\Exceptions\CharacterClass\RangeStartGreaterThanEndException;

/**
 * Adds character class functionality to the Regine builder.
 */
trait HasCharacterClasses
{
    /**
     * Adds a character class that matches any of the provided characters.
     *
     * @param  string  $chars  The characters to include inside the class, e.g. "abc" → `[abc]`.
     * @return self Fluent builder instance for chaining.
     *
     * @throws EmptyCharacterClassException If the character string is empty.
     */
    public function anyOf(string $chars): self
    {
        $this->elements->add(CharacterClassComponent::anyOf($chars));

        return $this;
    }

    /**
     * Adds a negated character class that matches none of the provided characters.
     *
     * @param  string  $chars  The characters to exclude inside the class, e.g. "abc" → `[^abc]`.
     * @return self Fluent builder instance for chaining.
     *
     * @throws EmptyCharacterClassException If the character string is empty.
     */
    public function noneOf(string $chars): self
    {
        $this->elements->add(CharacterClassComponent::noneOf($chars));

        return $this;
    }

    /**
     * Adds a character range from one character to another (inclusive).
     *
     * @param  string  $from  The starting character of the range (single UTF-8 character).
     * @param  string  $to  The ending character of the range (single UTF-8 character).
     * @return self Fluent builder instance for chaining.
     *
     * @throws RangeBoundariesAreNotSetException If the range boundaries are not set.
     * @throws RangeBoundariesNotSingleCharacterException If the range boundaries are not single characters.
     * @throws RangeBoundariesNotValidUTF8Exception If the range boundaries are not valid UTF-8 characters.
     * @throws RangeStartGreaterThanEndException If the range start is greater than the range end.
     */
    public function range(string $from, string $to): self
    {
        $this->elements->add(CharacterClassComponent::range($from, $to));

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
     * @throws RangeBoundariesAreNotSetException If the range boundaries are not set.
     * @throws RangeBoundariesNotSingleCharacterException If the range boundaries are not single characters.
     * @throws RangeBoundariesNotValidUTF8Exception If the range boundaries are not valid UTF-8 characters.
     * @throws RangeStartGreaterThanEndException If the range start is greater than the range end.
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
        $this->elements->add(CharacterClassComponent::noneOfRange($from, $to));

        return $this;
    }
}
