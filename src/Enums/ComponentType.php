<?php

declare(strict_types=1);

namespace Regine\Enums;

enum ComponentType: string
{
    case LITERAL = 'literal';
    case ANCHOR = 'anchor';
    case CHARACTER_CLASS = 'character_class';
    case RAW_PATTERN = 'raw_pattern';
    case ALTERNATION = 'alternation';
}