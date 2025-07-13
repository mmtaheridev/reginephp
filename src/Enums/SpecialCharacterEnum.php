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
     * Get all special characters that need escaping
     *
     * @return array<SpecialCharacterEnum>
     */
    public static function getAll(): array
    {
        return self::cases();
    }

    /**
     * Check if a character is a special regex character
     */
    public static function isSpecial(string $char): bool
    {
        return self::tryFrom($char) !== null;
    }

    /**
     * Get the enum case for a character if it's special
     */
    public static function fromChar(string $char): ?self
    {
        return self::tryFrom($char);
    }

    /**
     * Get the escaped version of the character for use in regex
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
     * Get the character class escaped version (different rules for character classes)
     */
    public function escapedForCharacterClass(): string
    {
        return match ($this) {
            self::BACKSLASH => '\\\\',
            self::RIGHT_BRACKET => '\]',
            self::CARET => '\^',
            self::DASH => '\-',
            default => $this->value,
        };
    }
}
