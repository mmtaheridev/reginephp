<?php

declare(strict_types=1);

namespace Regine\Enums;

enum SpecialCharacterEnum: string
{
    case DOT = '.';
    case ASTERISK = '*';
    case PLUS = '+';
    case QUESTION = '?';
    case CARET = '^';
    case DOLLAR = '$';
    case BACKSLASH = '\\';
    case PIPE = '|';
    case LEFT_PAREN = '(';
    case RIGHT_PAREN = ')';
    case LEFT_BRACKET = '[';
    case RIGHT_BRACKET = ']';
    case LEFT_BRACE = '{';
    case RIGHT_BRACE = '}';
    case DASH = '-';
    case COLON = ':';
    case EXCLAMATION = '!';
    case EQUAL = '=';
    case LESS_THAN = '<';
    case GREATER_THAN = '>';
    case FORWARD_SLASH = '/';

    /**
     * Returns an array of all special character enum cases.
     *
     * @return array<SpecialCharacterEnum> List of all defined special character enum cases.
     */
    public static function getAll(): array
    {
        return self::cases();
    }

    /**
     * Determines whether the given character is a special regular expression character.
     *
     * @param  string  $char  The character to check.
     * @return bool True if the character is a special regex character; otherwise, false.
     */
    public static function isSpecial(string $char): bool
    {
        return self::tryFrom($char) !== null;
    }

    /**
     * Returns the enum case corresponding to the given character if it is a special character.
     *
     * @param  string  $char  The character to check.
     * @return self|null The matching enum case, or null if the character is not special.
     */
    public static function fromChar(string $char): ?self
    {
        return self::tryFrom($char);
    }

    /**
     * Returns the regex-escaped string representation of the special character.
     *
     * @return string The character escaped for use in regular expressions.
     */
    public function escaped(): string
    {
        return match ($this) {
            self::DOT => '\.',
            self::ASTERISK => '\*',
            self::PLUS => '\+',
            self::QUESTION => '\?',
            self::CARET => '\^',
            self::DOLLAR => '\$',
            self::BACKSLASH => '\\\\',
            self::PIPE => '\|',
            self::LEFT_PAREN => '\(',
            self::RIGHT_PAREN => '\)',
            self::LEFT_BRACKET => '\[',
            self::RIGHT_BRACKET => '\]',
            self::LEFT_BRACE => '\{',
            self::RIGHT_BRACE => '\}',
            self::DASH => '\-',
            self::COLON => '\:',
            self::EXCLAMATION => '\!',
            self::EQUAL => '\=',
            self::LESS_THAN => '\<',
            self::GREATER_THAN => '\>',
            self::FORWARD_SLASH => '\/',
        };
    }

    /**
     * Returns the character escaped appropriately for use inside a regex character class.
     *
     * Only certain characters (`\`, `]`, `^`, `-`) are escaped; all others are returned as-is.
     *
     * @return string The character class-escaped version of the special character.
     */
    public function escapedForCharacterClass(): string
    {
        return match ($this) {
            self::BACKSLASH => '\\\\',
            self::RIGHT_BRACKET => '\]',
            self::CARET => '\^',
            self::DASH => '\-',
            self::LEFT_BRACKET => '\[',
            default => $this->value,
        };
    }
}
