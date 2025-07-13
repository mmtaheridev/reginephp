<?php

declare(strict_types=1);

namespace Regine\Enums;

enum ShorthandTypesEnum: string
{
    case ANY_CHAR = 'anyChar';
    case DIGIT = 'digit';
    case NON_DIGIT = 'nonDigit';
    case WORD_CHAR = 'wordChar';
    case NON_WORD_CHAR = 'nonWordChar';
    case WHITESPACE = 'whitespace';
    case NON_WHITESPACE = 'nonWhitespace';
    case LETTER = 'letter';

    public function getRegex(): string
    {
        return match ($this) {
            self::ANY_CHAR => '.',
            self::DIGIT => '\d',
            self::NON_DIGIT => '\D',
            self::WORD_CHAR => '\w',
            self::NON_WORD_CHAR => '\W',
            self::WHITESPACE => '\s',
            self::NON_WHITESPACE => '\S',
            self::LETTER => '[a-zA-Z]',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::ANY_CHAR => 'match any character',
            self::DIGIT => 'match any digit (0-9)',
            self::NON_DIGIT => 'match any non-digit',
            self::WORD_CHAR => 'match any word character (a-z, A-Z, 0-9, _)',
            self::NON_WORD_CHAR => 'match any non-word character',
            self::WHITESPACE => 'match any whitespace character',
            self::NON_WHITESPACE => 'match any non-whitespace character',
            self::LETTER => 'match any letter (a-z, A-Z)',
        };
    }
}
