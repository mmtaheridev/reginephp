<?php

declare(strict_types=1);

namespace Regine\Components;

use InvalidArgumentException;
use Regine\Contracts\RegexComponent;
use Regine\Enums\CharacterClassTypesEnum;
use Regine\ValueObjects\SafeCharacter;
use Regine\ValueObjects\SafeString;

/**
 * Character class component
 * 
 * Implements a regex component that represents a character class
 * (any of, none of, range)
 */
class CharacterClassComponent implements RegexComponent
{
    private static string $type = 'CHARACTER_CLASS';

    private SafeString $chars;
    private bool $negated;
    private CharacterClassTypesEnum $classType;

    public function __construct(string $chars, bool $negated, CharacterClassTypesEnum $type)
    {
        if ($chars === '') {
            throw new InvalidArgumentException('Character class cannot be empty.');
        }
        $this->chars = SafeString::from($chars);
        $this->negated = $negated;
        $this->classType = $type;
    }

    /**
     * Create a character class component for `any of` a set of characters
     */
    public static function anyOf(string $chars): self
    {
        return new self($chars, false, CharacterClassTypesEnum::ANY_OF);
    }

    /**
     * Create a character class component for `none of` a set of characters
     */
    public static function noneOf(string $chars): self
    {
        return new self($chars, true, CharacterClassTypesEnum::NONE_OF);
    }

    /**
     * Create a character class component for a range of characters
     */
    public static function range(string $from, string $to): self
    {
        if (strlen($from) !== 1 || strlen($to) !== 1) {
            throw new InvalidArgumentException('Range boundaries must be single characters.');
        }
        if ($from > $to) {
            throw new InvalidArgumentException('Range start must be less than or equal to range end.');
        }

        return new self($from . '-' . $to, false, CharacterClassTypesEnum::RANGE);
    }

    /**
     * Compile the character class component into a regex string
     */
    public function compile(): string
    {
        $prefix = $this->negated ? '^' : '';

        // For ranges, handle the dash specially - it should not be escaped as it's the range operator
        if ($this->classType === CharacterClassTypesEnum::RANGE) {
            // Extract the range components
            $rawChars = $this->chars->getRaw();

            // For ranges, we know the format is "from-to", so take first and last character
            // with the dash in between
            if (strlen($rawChars) >= 3) {
                $from = $rawChars[0];
                $to = $rawChars[2];

                // Create safe characters for from and to, then escape them individually
                $fromSafe = SafeString::from($from);
                $toSafe = SafeString::from($to);
                $escapedChars = $fromSafe->escapedForCharacterClass() . '-' . $toSafe->escapedForCharacterClass();
            } else {
                // Fallback to regular escaping if format is unexpected
                $escapedChars = $this->chars->escapedForCharacterClass();
            }
        } else {
            // For non-range character classes, use regular escaping
            $escapedChars = $this->chars->escapedForCharacterClass();
        }

        return '[' . $prefix . $escapedChars . ']';
    }

    /**
     * Get the type of the character class component
     */
    public function getType(): string
    {
        return self::$type;
    }

    /**
     * Get metadata about the character class component
     *
     * @return array<type: string, chars: string, negated: bool, classType: string, hasSpecialCharacters: bool, specialCharacters: array<string>>
     */
    public function getMetadata(): array
    {
        return [
            'type' => self::$type,
            'chars' => $this->chars->getRaw(),
            'negated' => $this->negated,
            'classType' => $this->classType->value,
            'hasSpecialCharacters' => $this->chars->hasSpecialCharacters(),
            'specialCharacters' => array_map(
                fn (SafeCharacter $char): string => $char->getRaw(),
                $this->chars->getSpecialCharacters()
            ),
        ];
    }

    /**
     * Check if the character class component can be quantified
     */
    public function canBeQuantified(): bool
    {
        return true;
    }

    /**
     * Get a human-readable description of the character class component
     */
    public function getDescription(): string
    {
        if ($this->classType === CharacterClassTypesEnum::RANGE) {
            $rawChars = $this->chars->getRaw();

            if (strlen($rawChars) >= 3) {
                $from = $rawChars[0];
                $to = $rawChars[2];

                return "Character range: from '{$from}' to '{$to}'";
            }
        }

        $action = $this->negated ? 'none of' : 'any of';

        return "Character class: {$action} '{$this->chars->getRaw()}'";
    }
}
