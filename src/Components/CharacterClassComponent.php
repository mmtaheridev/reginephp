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
 * (any of, none of, range, none of range)
 */
class CharacterClassComponent implements RegexComponent
{
    public const TYPE = 'CHARACTER_CLASS';

    private readonly SafeString $chars;
    private readonly bool $negated;
    private readonly CharacterClassTypesEnum $classType;

    /**
     * Initializes a new character class component with the specified characters, negation flag, and class type.
     *
     * @param  string  $chars  The characters to include in the character class.
     * @param  bool  $negated  Whether the character class is negated.
     * @param  CharacterClassTypesEnum  $type  The type of character class (e.g., any of, none of, range).
     *
     * @throws InvalidArgumentException If the character string is empty.
     */
    public function __construct(
        string $chars,
        bool $negated,
        CharacterClassTypesEnum $type
    ) {
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
     * @throws InvalidArgumentException If $chars is empty.
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
     * @throws InvalidArgumentException If $chars is empty.
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
     * @throws InvalidArgumentException If either boundary is not a single character or if the start is greater than the end.
     */
    public static function range(string $from, string $to): self
    {
        static::validateRange($from, $to);

        return new self($from . '-' . $to, false, CharacterClassTypesEnum::RANGE);
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
     * @throws InvalidArgumentException If boundaries are not single characters or start > end.
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
            type: CharacterClassTypesEnum::RANGE
        );
    }

    /**
     * Validates the range boundaries.
     *
     * @param  string  $from  The starting character of the range.
     * @param  string  $to  The ending character of the range.
     *
     * @throws InvalidArgumentException If the range boundaries are not single characters.
     */
    protected static function validateRange(string $from, string $to): void
    {
        // Validate that the range boundaries are single characters
        if (mb_strlen($from, 'UTF-8') !== 1 || mb_strlen($to, 'UTF-8') !== 1) {
            throw new InvalidArgumentException('Range boundaries must be single characters.');
        }

        // Validate that the range boundaries are valid UTF-8 characters
        $fromCodePoint = mb_ord($from, 'UTF-8');
        $toCodePoint = mb_ord($to, 'UTF-8');

        if ($fromCodePoint === false || $toCodePoint === false) {
            throw new InvalidArgumentException('Range boundaries must be valid UTF-8 characters.');
        }

        // Validate that the range start is less than or equal to the range end
        if ($fromCodePoint > $toCodePoint) {
            throw new InvalidArgumentException('Range start must be less than or equal to range end.');
        }
    }

    /**
     * Compiles the character class component into a regex character class string.
     *
     * Handles negation and character ranges, escaping characters as needed for safe inclusion in a regex pattern.
     * For ranges, the internal raw string is split on the first dash ("-") to extract the two sides safely.
     * For variable-length parts (e.g., "ab-cd"), it uses the first character of the left part ("a") and the
     * last character of the right part ("d") as the actual range endpoints, escaping each endpoint individually.
     * If the format is unexpected (no dash or missing endpoints), it falls back to normal character-class escaping.
     *
     * @return string The compiled regex character class.
     */
    public function compile(): string
    {
        $prefix = $this->negated ? '^' : '';

        // For ranges, handle the dash specially - it should not be escaped as it's the range operator
        if ($this->classType === CharacterClassTypesEnum::RANGE) {
            $escapedChars = $this->handleRangeCharacterClass();
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
        return static::TYPE;
    }

    /**
     * Returns metadata describing the character class component, including type, raw characters, negation status, class type, and special character details.
     *
     * @return array<string, mixed> Associative array with keys: 'type', 'chars', 'negated', 'classType', 'hasSpecialCharacters', 'specialCharacters', and 'specialCharactersEscaped'.
     */
    public function getMetadata(): array
    {
        return [
            'type' => static::TYPE,
            'chars' => $this->chars->getRaw(),
            'negated' => $this->negated,
            'classType' => $this->classType->value,
            // Signal up to the builder that this component requires Unicode semantics
            // when multibyte characters are present (so the 'u' flag can be enforced).
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
     * For range types, describes the character range boundaries. Internally, the raw string is split on the first dash;
     * for variable-length parts (e.g., "ab-cd"), the description reflects the first character of the left part and the
     * last character of the right part ("a" to "d"). For other types, specifies whether the class matches any or none
     * of the given characters.
     *
     * @return string The description of the character class.
     */
    public function getDescription(): string
    {
        if ($this->classType === CharacterClassTypesEnum::RANGE) {
            $rawChars = $this->chars->getRaw();

            // Split on the first dash and safely extract endpoints (multibyte-aware)
            $parts = explode('-', $rawChars, 2);
            if (count($parts) === 2 && $parts[0] !== '' && $parts[1] !== '') {
                $from = mb_substr($parts[0], 0, 1, 'UTF-8');
                $rightLen = mb_strlen($parts[1], 'UTF-8');
                $to = mb_substr($parts[1], $rightLen - 1, 1, 'UTF-8');

                $prefix = $this->negated ? 'Negated character' : 'Character';

                return "{$prefix} range: from '{$from}' to '{$to}'";
            }
        }

        $action = $this->negated ? 'none of' : 'any of';

        return "Character class: {$action} '{$this->chars->getRaw()}'";
    }

    /**
     * Handles the range character class
     *
     * @return string The escaped characters for the range.
     */
    protected function handleRangeCharacterClass(): string
    {
        // Extract the range components
        $rawChars = $this->chars->getRaw();

        // Split on the first dash to safely extract start and end parts (multibyte-aware)
        $parts = explode('-', $rawChars, 2);

        if (count($parts) === 2 && $parts[0] !== '' && $parts[1] !== '') {
            // Take the first character of the left part and the last character of the right part
            $fromChar = mb_substr($parts[0], 0, 1, 'UTF-8');
            $rightLen = mb_strlen($parts[1], 'UTF-8');
            $toChar = mb_substr($parts[1], $rightLen - 1, 1, 'UTF-8');

            $fromSafe = SafeString::from($fromChar);
            $toSafe = SafeString::from($toChar);
            $escapedChars = $fromSafe->escapedForCharacterClass() . '-' . $toSafe->escapedForCharacterClass();
        } else {
            // Fallback to regular escaping if format is unexpected
            $escapedChars = $this->chars->escapedForCharacterClass();
        }

        return $escapedChars;
    }

    /**
     * Determines whether the character class contains any multibyte UTF-8 characters.
     *
     * Used to signal that Unicode mode ('u' flag) is required for correct semantics.
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
