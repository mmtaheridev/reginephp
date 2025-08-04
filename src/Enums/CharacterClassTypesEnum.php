<?php

declare(strict_types=1);

namespace Regine\Enums;

enum CharacterClassTypesEnum: string
{
    case ANY_OF = 'anyOf';
    case NONE_OF = 'noneOf';
    case RANGE = 'range';

    /**
     * Get the description for the character class type
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::ANY_OF => 'match any of',
            self::NONE_OF => 'match none of',
            self::RANGE => 'match character range',
        };
    }
}
