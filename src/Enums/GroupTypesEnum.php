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

    /**
     * Returns the regex prefix string corresponding to the group type.
     *
     * @return string The regex prefix for this group type.
     */
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

    /**
     * Returns the regex suffix string corresponding to the group type.
     *
     * @return string The regex suffix for this group type.
     */
    public function getRegexSuffix(): string
    {
        return match ($this) {
            self::CAPTURING,
            self::NON_CAPTURING,
            self::ATOMIC, self::CONDITIONAL => ')',
            self::NAMED => '>',
        };
    }

    /**
     * Returns a human-readable description of the regex group type represented by this enum case.
     *
     * @return string The description of the group type.
     */
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
