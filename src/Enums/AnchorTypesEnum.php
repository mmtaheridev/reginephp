<?php

declare(strict_types=1);

namespace Regine\Enums;

enum AnchorTypesEnum: string
{
    case START_OF_STRING = 'startOfString';
    case END_OF_STRING = 'endOfString';
    case START_OF_LINE = 'startOfLine';
    case END_OF_LINE = 'endOfLine';
    case WORD_BOUNDARY = 'wordBoundary';
    case NON_WORD_BOUNDARY = 'nonWordBoundary';

    public function getRegex(): string
    {
        return match ($this) {
            self::START_OF_STRING => '^',
            self::END_OF_STRING => '$',
            self::START_OF_LINE => '^',
            self::END_OF_LINE => '$',
            self::WORD_BOUNDARY => '\b',
            self::NON_WORD_BOUNDARY => '\B',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::START_OF_STRING => 'start of string',
            self::END_OF_STRING => 'end of string',
            self::START_OF_LINE => 'start of line',
            self::END_OF_LINE => 'end of line',
            self::WORD_BOUNDARY => 'word boundary',
            self::NON_WORD_BOUNDARY => 'non-word boundary',
        };
    }
}
