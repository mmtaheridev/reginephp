<?php

declare(strict_types=1);

namespace Regine\Enums;

enum GroupTypesEnum: string
{
    case CAPTURING = 'CAPTURING';
    case NON_CAPTURING = 'NON_CAPTURING';
    case NAMED = 'NAMED';
    case ATOMIC = 'ATOMIC';
    case CONDITIONAL = 'CONDITIONAL';

    public function getRegexPrefix(): string
    {
        return match ($this) {
            self::CAPTURING => '(',
            self::NON_CAPTURING => '(?:',
            self::NAMED => '(?<',
            self::ATOMIC => '(?>',
            self::CONDITIONAL => '(?(',
        };
    }

    public function getRegexSuffix(): string
    {
        return match ($this) {
            self::CAPTURING,
            self::NON_CAPTURING,
            self::ATOMIC => ')',
            self::NAMED => '>',
            self::CONDITIONAL => ')',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::CAPTURING => 'capturing group',
            self::NON_CAPTURING => 'non-capturing group',
            self::NAMED => 'named group',
            self::ATOMIC => 'atomic group',
            self::CONDITIONAL => 'conditional group',
        };
    }
}
