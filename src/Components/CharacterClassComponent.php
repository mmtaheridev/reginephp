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

    /**
     * Initializes a new character class component with the specified characters, negation flag, and class type.
     *
     * @param  string  $chars  The characters to include in the character class.
     * @param  bool  $negated  Whether the character class is negated.
     * @param  CharacterClassTypesEnum  $type  The type of character class (e.g., any of, none of, range).
     *
     * @throws InvalidArgumentException If the character string is empty.
     */
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
     * Creates a character class component that matches any of the specified characters.
     *
     * @param  string  $chars  The set of characters to include in the character class.
     * @return self A new CharacterClassComponent representing the specified character set.
     */
    public static function anyOf(string $chars): self
    {
        return new self($chars, false, CharacterClassTypesEnum::ANY_OF);
    }

    /**
     * Creates a character class component that matches none of the specified characters.
     *
     * @param  string  $chars  The characters to exclude from the match.
     * @return self The constructed character class component.
     */
    public static function noneOf(string $chars): self
    {
        return new self($chars, true, CharacterClassTypesEnum::NONE_OF);
    }

    /**
     * Creates a character class component representing a range from one character to another.
     *
     * @param  string  $from  The starting character of the range.
     * @param  string  $to  The ending character of the range.
     * @return self The character class component for the specified range.
     *
     * @throws InvalidArgumentException If either boundary is not a single character or if the start is greater than the end.
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
     * Compiles the character class component into a regex character class string.
     *
     * Handles negation and character ranges, escaping characters as needed for safe inclusion in a regex pattern.
     *
     * @return string The compiled regex character class.
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
     * Returns the type identifier for the character class component.
     *
     * @return string The static type string 'CHARACTER_CLASS'.
     */
    public function getType(): string
    {
        return self::$type;
    }

    /**
     * Returns metadata describing the character class component, including type, raw characters, negation status, class type, and special character details.
     *
     * @return array<string, mixed> Associative array with keys: 'type', 'chars', 'negated', 'classType', 'hasSpecialCharacters', and 'specialCharacters'.
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
     * Determines whether the character class component supports quantifiers.
     *
     * @return bool Always returns true, as character classes can be quantified in regular expressions.
     */
    public function canBeQuantified(): bool
    {
        return true;
    }

    /**
     * Returns a human-readable description of the character class, indicating its type and included or excluded characters.
     *
     * For range types, describes the character range boundaries. For other types, specifies whether the class matches any or none of the given characters.
     *
     * @return string The description of the character class.
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
