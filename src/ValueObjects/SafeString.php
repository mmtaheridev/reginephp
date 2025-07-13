<?php

declare(strict_types=1);

namespace Regine\ValueObjects;

readonly class SafeString
{
    /** @var SafeCharacter[] */
    private array $characters;

    /**
     * @param  SafeCharacter[]  $characters
     */
    private function __construct(array $characters)
    {
        $this->characters = $characters;
    }

    /**
     * Create a SafeString from a raw string
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
     * Create a SafeString from an array of SafeCharacter objects
     *
     * @param  array<SafeCharacter>  $characters
     */
    public static function fromSafeCharacters(array $characters): self
    {
        return new self($characters);
    }

    /**
     * Get the raw string value
     */
    public function getRaw(): string
    {
        return implode('', array_map(fn (SafeCharacter $char) => $char->getRaw(), $this->characters));
    }

    /**
     * Get the escaped version for regex compilation
     */
    public function escaped(): string
    {
        return implode('', array_map(fn (SafeCharacter $char) => $char->escaped(), $this->characters));
    }

    /**
     * Get the escaped version for character class compilation
     */
    public function escapedForCharacterClass(): string
    {
        return implode('', array_map(fn (SafeCharacter $char) => $char->escapedForCharacterClass(), $this->characters));
    }

    /**
     * Check if the string contains any special characters
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
     * Get all special characters in the string
     *
     * @return array<SafeCharacter>
     */
    public function getSpecialCharacters(): array
    {
        return array_filter($this->characters, fn (SafeCharacter $char) => $char->isSpecial());
    }

    /**
     * Get the characters array
     *
     * @return array<SafeCharacter>
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * Check if the string is empty
     */
    public function isEmpty(): bool
    {
        return $this->characters === [];
    }

    /**
     * Get the length of the string
     */
    public function length(): int
    {
        return count($this->characters);
    }

    /**
     * String representation returns the raw string
     */
    public function __toString(): string
    {
        return $this->getRaw();
    }
}
