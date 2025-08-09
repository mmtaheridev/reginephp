<?php

declare(strict_types=1);

namespace Regine\ValueObjects;

readonly class SafeString
{
    /** @var SafeCharacter[] */
    private array $characters;

    /**
     * Initializes a SafeString with an array of SafeCharacter objects.
     *
     * @param  SafeCharacter[]  $characters  Array of SafeCharacter instances to compose the SafeString.
     */
    private function __construct(array $characters)
    {
        $this->characters = $characters;
    }

    /**
     * Creates a SafeString instance from a raw string by converting each character to a SafeCharacter.
     *
     * Returns an empty SafeString if the input string is empty.
     *
     * @param  string  $text  The raw string to convert.
     * @return self The resulting SafeString instance.
     */
    public static function from(string $text): self
    {
        if (strlen($text) === 0) {
            return new self([]);
        }

        $characters = [];
        $length = strlen($text);

        for ($i = 0; $i < $length; $i++) {
            $characters[] = SafeCharacter::from($text[$i]);
        }

        return new self($characters);
    }

    /**
     * Creates a SafeString from an array of SafeCharacter objects.
     *
     * @param  array<SafeCharacter>  $characters  The array of SafeCharacter instances to compose the SafeString.
     * @return self A new SafeString instance containing the provided characters.
     */
    public static function fromSafeCharacters(array $characters): self
    {
        return new self($characters);
    }

    /**
     * Returns the raw string composed of all characters in the SafeString.
     *
     * @return string The concatenated raw string value.
     */
    public function getRaw(): string
    {
        return implode('', array_map(fn (SafeCharacter $char) => $char->getRaw(), $this->characters));
    }

    /**
     * Returns the string with all characters escaped for use in regular expression patterns.
     *
     * Each character is escaped according to its regex requirements, making the result safe for direct inclusion in regex patterns.
     *
     * @return string The escaped string suitable for regex compilation.
     */
    public function escaped(): string
    {
        return implode('', array_map(fn (SafeCharacter $char) => $char->escaped(), $this->characters));
    }

    /**
     * Returns the string with all characters escaped for use in a regex character class.
     *
     * Each character is escaped using its `escapedForCharacterClass()` method to ensure safe inclusion in a regular expression character class.
     *
     * @return string The escaped string suitable for regex character class compilation.
     */
    public function escapedForCharacterClass(): string
    {
        return implode('', array_map(fn (SafeCharacter $char) => $char->escapedForCharacterClass(), $this->characters));
    }

    /**
     * Determines whether the string contains any special characters.
     *
     * @return bool True if at least one character is special; otherwise, false.
     */
    public function hasSpecialCharacters(): bool
    {
        foreach ($this->characters as $char) {
            if ($char->isSpecial()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an array of all special characters contained in the string.
     *
     * @return array<SafeCharacter> The special characters present in the string.
     */
    public function getSpecialCharacters(): array
    {
        return array_filter($this->characters, fn (SafeCharacter $char) => $char->isSpecial());
    }

    /**
     * Returns the array of `SafeCharacter` objects that make up this `SafeString`.
     *
     * @return array<SafeCharacter> The internal array of `SafeCharacter` instances.
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * Determines whether the SafeString contains no characters.
     *
     * @return bool True if the SafeString is empty; otherwise, false.
     */
    public function isEmpty(): bool
    {
        return $this->characters === [];
    }

    /**
     * Returns the number of characters in the SafeString.
     *
     * @return int The length of the SafeString.
     */
    public function length(): int
    {
        return count($this->characters);
    }

    /**
     * Returns the raw string representation of the SafeString.
     *
     * @return string The concatenated raw string value.
     */
    public function __toString(): string
    {
        return $this->getRaw();
    }
}
