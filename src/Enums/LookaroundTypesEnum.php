<?php

declare(strict_types=1);

namespace Regine\Enums;

enum LookaroundTypesEnum: string
{
    case POSITIVE_LOOKAHEAD = 'positive_lookahead';
    case NEGATIVE_LOOKAHEAD = 'negative_lookahead';
    case POSITIVE_LOOKBEHIND = 'positive_lookbehind';
    case NEGATIVE_LOOKBEHIND = 'negative_lookbehind';

    /**
     * Returns the regex lookaround pattern for this type, embedding the given content.
     *
     * @param  string  $content  The regex content to include within the lookaround assertion.
     * @return string The complete regex lookaround pattern for this enum case.
     */
    public function getPattern(string $content): string
    {
        return match ($this) {
            self::POSITIVE_LOOKAHEAD => "(?={$content})",
            self::NEGATIVE_LOOKAHEAD => "(?!{$content})",
            self::POSITIVE_LOOKBEHIND => "(?<={$content})",
            self::NEGATIVE_LOOKBEHIND => "(?<!{$content})",
        };
    }

    /**
     * Returns a human-readable description of the lookaround type represented by this enum instance.
     *
     * @return string The description of the lookaround type.
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::POSITIVE_LOOKAHEAD => 'positive lookahead',
            self::NEGATIVE_LOOKAHEAD => 'negative lookahead',
            self::POSITIVE_LOOKBEHIND => 'positive lookbehind',
            self::NEGATIVE_LOOKBEHIND => 'negative lookbehind',
        };
    }

    /**
     * Returns the regex prefix corresponding to the lookaround type.
     *
     * @return string The prefix used to start the regex lookaround assertion for this enum case.
     */
    public function getPrefix(): string
    {
        return match ($this) {
            self::POSITIVE_LOOKAHEAD => '(?=',
            self::NEGATIVE_LOOKAHEAD => '(?!',
            self::POSITIVE_LOOKBEHIND => '(?<=',
            self::NEGATIVE_LOOKBEHIND => '(?<!',
        };
    }
}
