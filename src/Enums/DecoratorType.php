<?php

declare(strict_types=1);

namespace Regine\Enums;

/**
 * Enumeration of decorator types for the Component-Decorator architecture.
 *
 * Defines the different types of decorators and their precedence order.
 * Lower precedence values are applied first (closer to the component).
 */
enum DecoratorType: string
{
    case QUANTIFIER = 'quantifier';
    case GROUP = 'group';
    case LOOKAROUND = 'lookaround';

    /**
     * Get the precedence level for this decorator type.
     * Lower numbers have higher precedence (applied first).
     */
    public function getPrecedence(): int
    {
        return match ($this) {
            self::QUANTIFIER => 1,  // Applied first (closest to component)
            self::GROUP => 2,       // Applied second
            self::LOOKAROUND => 3,  // Applied last (outermost)
        };
    }

    /**
     * Get a human-readable description of this decorator type.
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::QUANTIFIER => 'Quantifier (*, +, ?, {n,m})',
            self::GROUP => 'Group (capturing, non-capturing, named)',
            self::LOOKAROUND => 'Lookaround (lookahead, lookbehind)',
        };
    }
}
