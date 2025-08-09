<?php

declare(strict_types=1);

namespace Regine\ValueObjects;

use InvalidArgumentException;
use Regine\Enums\SpecialCharacterEnum;

readonly class SafeCharacter
{
    /**
     * Initializes a SafeCharacter instance with a raw character and optional special character enum.
     *
     * @param  string  $value  The single character to encapsulate.
     * @param  SpecialCharacterEnum|null  $specialChar  The associated special character enum, or null if not special.
     */
    private function __construct(
        private string $value,
        private ?SpecialCharacterEnum $specialChar = null
    ) {}

    /**
     * Creates a SafeCharacter instance from a single-character string.
     *
     * Throws an InvalidArgumentException if the input string is not exactly one character long.
     *
     * @param  string  $char  The character to wrap.
     * @return self The SafeCharacter instance representing the given character.
     *
     * @throws InvalidArgumentException If the input string is not a single character.
     */
    public static function from(string $char): self
    {
        if (mb_strlen($char, 'UTF-8') !== 1) {
            throw new InvalidArgumentException('SafeCharacter can only wrap single characters');
        }

        $special = SpecialCharacterEnum::fromChar($char);

        return new self($char, $special);
    }

    /**
     * Returns the raw character string encapsulated by this object.
     *
     * @return string The single character value.
     */
    public function getRaw(): string
    {
        return $this->value;
    }

    /**
     * Determines whether the character is recognized as a special character.
     *
     * @return bool True if the character has an associated SpecialCharacterEnum; otherwise, false.
     */
    public function isSpecial(): bool
    {
        return $this->specialChar !== null;
    }

    /**
     * Returns the associated special character enum if the character is special.
     *
     * @return SpecialCharacterEnum|null The corresponding SpecialCharacterEnum instance, or null if the character is not special.
     */
    public function getSpecialChar(): ?SpecialCharacterEnum
    {
        return $this->specialChar;
    }

    /**
     * Returns the escaped string representation of the character for use in regular expression patterns.
     *
     * If the character is recognized as special, its escaped form from the associated enum is returned; otherwise, the raw character is returned.
     *
     * @return string The escaped character suitable for regex compilation.
     */
    public function escaped(): string
    {
        return $this->specialChar?->escaped() ?? $this->value;
    }

    /**
     * Returns the escaped representation of the character suitable for use in a regex character class.
     *
     * If the character is recognized as special, its corresponding escaped value is returned; otherwise, the raw character is returned.
     *
     * @return string The escaped character for regex character class usage.
     */
    public function escapedForCharacterClass(): string
    {
        return $this->specialChar?->escapedForCharacterClass() ?? $this->value;
    }

    /**
     * Returns the raw character as a string.
     *
     * When the object is used in a string context, this method returns the encapsulated character.
     *
     * @return string The raw character.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
