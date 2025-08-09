<?php

declare(strict_types=1);

namespace Regine\Enums;

enum QuantifierTypesEnum: string
{
    case ZERO_OR_MORE = 'zeroOrMore';
    case ONE_OR_MORE = 'oneOrMore';
    case OPTIONAL = 'optional';
    case EXACTLY = 'exactly';
    case AT_LEAST = 'atLeast';
    case BETWEEN = 'between';

    /**
     * Returns a human-readable description of the quantifier type, with placeholders for required parameters.
     *
     * @return string The description of the quantifier, using placeholders such as {count}, {min}, or {max} where applicable.
     */
    public function getBaseDescription(): string
    {
        return match ($this) {
            self::ZERO_OR_MORE => 'zero or more times',
            self::ONE_OR_MORE => 'one or more times',
            self::OPTIONAL => 'zero or one time',
            self::EXACTLY => 'exactly {count} times',
            self::AT_LEAST => 'at least {min} times',
            self::BETWEEN => 'between {min} and {max} times',
        };
    }

    /**
     * Returns the regex quantifier string corresponding to the enum case.
     *
     * @param  array<string, int>  $params  Optional parameters for quantifiers that require counts, such as 'count', 'min', or 'max'.
     * @return string The regex quantifier string for this quantifier type.
     */
    public function getRegex(array $params = []): string
    {
        return match ($this) {
            self::ZERO_OR_MORE => '*',
            self::ONE_OR_MORE => '+',
            self::OPTIONAL => '?',
            self::EXACTLY => '{' . ($params['count'] ?? 0) . '}',
            self::AT_LEAST => '{' . ($params['min'] ?? 0) . ',}',
            self::BETWEEN => '{' . ($params['min'] ?? 0) . ',' . ($params['max'] ?? 0) . '}',
        };
    }
}
