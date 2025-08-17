<?php

declare(strict_types=1);

namespace Regine\Components;

use Regine\Contracts\RegexComponent;
use Regine\Enums\CharacterClassTypesEnum;
use Regine\Enums\ComponentType;
use Regine\Exceptions\CharacterClass\EmptyCharacterClassException;
use Regine\Exceptions\CharacterClass\RangeBoundariesAreNotSetException;
use Regine\Exceptions\CharacterClass\RangeBoundariesNotSingleCharacterException;
use Regine\Exceptions\CharacterClass\RangeBoundariesNotValidUTF8Exception;
use Regine\Exceptions\CharacterClass\RangeStartGreaterThanEndException;
use Regine\ValueObjects\SafeCharacter;
use Regine\ValueObjects\SafeString;

/**
 * Character class component
 *
 * Implements a regex component that represents a character class
 * (any of, none of, range, none of range)
 */
class CharacterClassComponent implements RegexComponent
{
    private readonly SafeString $chars;
    private readonly bool $negated;
    private readonly CharacterClassTypesEnum $classType;
    private readonly ?SafeCharacter $fromChar;
    private readonly ?SafeCharacter $toChar;

    /**
     * Initializes a new character class component with the specified characters, negation flag, and class type.
     *
     * @param  string  $chars  The characters to include in the character class.
     * @param  bool  $negated  Whether the character class is negated.
     * @param  CharacterClassTypesEnum  $type  The type of character class (e.g., any of, none of, range).
     * @param  string|null  $fromChar  For RANGE type, the start character of the range.
     * @param  string|null  $toChar  For RANGE type, the end character of the range.
     *
     * @throws EmptyCharacterClassException If the character string is empty or if RANGE type lacks endpoints.
     * @throws RangeBoundariesAreNotSetException If type is RANGE and the range boundaries are not set.
     * @throws RangeBoundariesNotSingleCharacterException If type is RANGE and the range boundaries are not single characters.
     * @throws RangeBoundariesNotValidUTF8Exception If type is RANGE and the range boundaries are not valid UTF-8 characters.
     * @throws RangeStartGreaterThanEndException If type is RANGE and the range start is greater than the range end.
     */
    public function __construct(
        string $chars,
        bool $negated,
        CharacterClassTypesEnum $type,
        ?string $fromChar = null,
        ?string $toChar = null
    ) {
        if ($chars === '') {
            throw new EmptyCharacterClassException;
        }

        $this->chars = SafeString::from($chars);
        $this->negated = $negated;
        $this->classType = $type;

        // For RANGE type, store explicit endpoints
        if ($this->classType === CharacterClassTypesEnum::RANGE) {
            if ($fromChar === null || $toChar === null) {
                throw new RangeBoundariesAreNotSetException;
            }
            $this->fromChar = SafeCharacter::from($fromChar);
            $this->toChar = SafeCharacter::from($toChar);
        } else {
            $this->fromChar = null;
            $this->toChar = null;
        }
    }

    /**
     * Creates a character class component that matches any of the specified characters.
     *
     * @param  string  $chars  The set of characters to include in the character class.
     * @return self A new CharacterClassComponent representing the specified character set.
     *
     * @throws EmptyCharacterClassException If $chars is empty.
     */
    public static function anyOf(string $chars): self
    {
        return new self(
            chars: $chars,
            negated: false,
            type: CharacterClassTypesEnum::ANY_OF
        );
    }

    /**
     * Creates a character class component that matches none of the specified characters.
     *
     * @param  string  $chars  The characters to exclude from the match.
     * @return self The constructed character class component.
     *
     * @throws EmptyCharacterClassException If $chars is empty.
     */
    public static function noneOf(string $chars): self
    {
        return new self(
            chars: $chars,
            negated: true,
            type: CharacterClassTypesEnum::NONE_OF
        );
    }

    /**
     * Creates a character class component representing a range from one character to another.
     *
     * @param  string  $from  The starting character of the range.
     * @param  string  $to  The ending character of the range.
     * @return self The character class component for the specified range.
     *
     * @throws RangeBoundariesNotSingleCharacterException If the range boundaries are not single characters.
     * @throws RangeBoundariesNotValidUTF8Exception If the range boundaries are not valid UTF-8 characters.
     * @throws RangeStartGreaterThanEndException If the range start is greater than the range end.
     */
    public static function range(string $from, string $to): self
    {
        static::validateRange($from, $to);

        return new self(
            chars: $from . '-' . $to,
            negated: false,
            type: CharacterClassTypesEnum::RANGE,
            fromChar: $from,
            toChar: $to
        );
    }

    /**
     * Creates a negated character range (e.g., [^a-z]).
     *
     * Produces a character class that matches any character not in the given inclusive range.
     *
     * @param  string  $from  The starting character of the range (single UTF-8 character).
     * @param  string  $to  The ending character of the range (single UTF-8 character).
     * @return self The character class component for the specified negated range.
     *
     * @throws RangeBoundariesNotSingleCharacterException If the range boundaries are not single characters.
     * @throws RangeBoundariesNotValidUTF8Exception If the range boundaries are not valid UTF-8 characters.
     * @throws RangeStartGreaterThanEndException If the range start is greater than the range end.
     *
     * <code>
     * // Matches one or more non-digit characters
     * $regex = Regine::make()
     *     ->noneOfRange('0', '9')
     *     ->oneOrMore()
     *     ->compile(); // '/[^0-9]+/'
     * </code>
     */
    public static function noneOfRange(string $from, string $to): self
    {
        static::validateRange($from, $to);

        return new self(
            chars: $from . '-' . $to,
            negated: true,
            type: CharacterClassTypesEnum::RANGE,
            fromChar: $from,
            toChar: $to
        );
    }

    /**
     * Validates the range boundaries.
     *
     * @param  string  $from  The starting character of the range.
     * @param  string  $to  The ending character of the range.
     *
     * @throws RangeBoundariesNotSingleCharacterException If the range boundaries are not single characters.
     * @throws RangeBoundariesNotValidUTF8Exception If the range boundaries are not valid UTF-8 characters.
     * @throws RangeStartGreaterThanEndException If the range start is greater than the range end.
     */
    protected static function validateRange(string $from, string $to): void
    {
        // Validate that the range boundaries are single characters
        if (mb_strlen($from, 'UTF-8') !== 1 || mb_strlen($to, 'UTF-8') !== 1) {
            throw new RangeBoundariesNotSingleCharacterException;
        }

        // Validate that the range boundaries are valid UTF-8 characters
        $fromCodePoint = mb_ord($from, 'UTF-8');
        $toCodePoint = mb_ord($to, 'UTF-8');

        if ($fromCodePoint === false || $toCodePoint === false) {
            throw new RangeBoundariesNotValidUTF8Exception;
        }

        // Validate that the range start is less than or equal to the range end
        if ($fromCodePoint > $toCodePoint) {
            throw new RangeStartGreaterThanEndException;
        }
    }

    /**
     * Compiles the character class component into a regex character class string.
     *
     * For ranges, uses the stored explicit endpoints and escapes them properly.
     * For other types, uses regular character class escaping.
     *
     * @return string The compiled regex character class.
     *
     * @throws RangeBoundariesAreNotSetException If the RANGE type lacks endpoints.
     */
    public function compile(): string
    {
        $prefix = $this->negated ? '^' : '';

        if ($this->classType === CharacterClassTypesEnum::RANGE) {
            if ($this->fromChar === null || $this->toChar === null) {
                throw new RangeBoundariesAreNotSetException;
            }

            // Use stored explicit endpoints for ranges
            $fromEscaped = $this->fromChar->escapedForCharacterClass();
            $toEscaped = $this->toChar->escapedForCharacterClass();

            return '[' . $prefix . $fromEscaped . '-' . $toEscaped . ']';
        }

        // For non-range character classes, use regular escaping
        $escapedChars = $this->chars->escapedForCharacterClass();

        return '[' . $prefix . $escapedChars . ']';
    }

    /**
     * Returns the type identifier for the character class component.
     *
     * @return string The static type string 'CHARACTER_CLASS'.
     */
    public function getType(): string
    {
        return ComponentType::CHARACTER_CLASS->value;
    }

    /**
     * Returns metadata describing the character class component, including type, raw characters, negation status, class type, and special character details.
     *
     * @return array<string, mixed> Associative array with keys: 'type', 'chars', 'negated', 'classType', 'hasSpecialCharacters', 'specialCharacters', and 'specialCharactersEscaped'.
     *
     * @throws RangeBoundariesAreNotSetException If the RANGE type lacks endpoints.
     */
    public function getMetadata(): array
    {
        $metadata = [
            'type' => $this->getType(),
            'chars' => $this->chars->getRaw(),
            'negated' => $this->negated,
            'classType' => $this->classType->value,
            'requiresUnicode' => $this->containsMultibyteCharacters(),
            'hasSpecialCharacters' => $this->chars->hasSpecialCharacters(),
            'specialCharacters' => array_map(
                fn (SafeCharacter $char): string => $char->getRaw(),
                $this->chars->getSpecialCharacters()
            ),
            'specialCharactersEscaped' => array_map(
                fn (SafeCharacter $char): string => $char->escapedForCharacterClass(),
                $this->chars->getSpecialCharacters()
            ),
        ];

        // Add range-specific metadata
        if ($this->classType === CharacterClassTypesEnum::RANGE) {
            if ($this->fromChar === null || $this->toChar === null) {
                throw new RangeBoundariesAreNotSetException;
            }

            $metadata['fromChar'] = $this->fromChar->getRaw();
            $metadata['toChar'] = $this->toChar->getRaw();
            $metadata['fromCharCode'] = mb_ord($this->fromChar->getRaw());
            $metadata['toCharCode'] = mb_ord($this->toChar->getRaw());
        }

        return $metadata;
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
     * For range types, describes the actual range boundaries. For other types, specifies whether the class matches
     * any or none of the given characters.
     *
     * @return string The description of the character class.
     *
     * @throws RangeBoundariesAreNotSetException If the RANGE type lacks endpoints.
     */
    public function getDescription(): string
    {
        if ($this->classType === CharacterClassTypesEnum::RANGE) {
            if ($this->fromChar === null || $this->toChar === null) {
                throw new RangeBoundariesAreNotSetException;
            }

            $from = $this->fromChar->getRaw();
            $to = $this->toChar->getRaw();
            $prefix = $this->negated ? 'Negated character' : 'Character';

            return "{$prefix} range: from '{$from}' to '{$to}'";
        }

        $action = $this->negated ? 'none of' : 'any of';

        return "Character class: {$action} '{$this->chars->getRaw()}'";
    }

    /**
     * Determines whether the character class contains any multibyte UTF-8 characters.
     *
     * Used to signal that Unicode mode ('u' flag) is required for correct semantics.
     *
     * @return bool True if the character class contains any multibyte UTF-8 characters, false otherwise.
     */
    private function containsMultibyteCharacters(): bool
    {
        foreach ($this->chars->getCharacters() as $character) {
            // If the underlying byte-length is not 1, it's a multibyte character in UTF-8
            if (strlen($character->getRaw()) !== 1) {
                return true;
            }
        }

        return false;
    }
}
