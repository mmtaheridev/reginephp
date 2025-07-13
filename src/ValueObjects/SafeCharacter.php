<?php

declare(strict_types=1);

namespace Regine\ValueObjects;

use InvalidArgumentException;
use Regine\Enums\SpecialCharacterEnum;

readonly class SafeCharacter
{
    private function __construct(
        private string $value,
        private ?SpecialCharacterEnum $specialChar = null
    ) {}

    /**
     * Create a safe character from a raw character
     */
    public static function from(string $char): self
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('SafeCharacter can only wrap single characters');
        }

        $special = SpecialCharacterEnum::fromChar($char);

        return new self($char, $special);
    }

    /**
     * Get the raw character value
     */
    public function getRaw(): string
    {
        return $this->value;
    }

    /**
     * Check if this character is special
     */
    public function isSpecial(): bool
    {
        return $this->specialChar !== null;
    }

    /**
     * Get the special character enum if this is a special character
     */
    public function getSpecialChar(): ?SpecialCharacterEnum
    {
        return $this->specialChar;
    }

    /**
     * Get the escaped version for regex compilation
     */
    public function escaped(): string
    {
        return $this->specialChar?->escaped() ?? $this->value;
    }

    /**
     * Get the escaped version for character class compilation
     */
    public function escapedForCharacterClass(): string
    {
        return $this->specialChar?->escapedForCharacterClass() ?? $this->value;
    }

    /**
     * String representation returns the raw character
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
