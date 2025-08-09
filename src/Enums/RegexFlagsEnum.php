<?php

declare(strict_types=1);

namespace Regine\Enums;

/**
 * This enum represents the available flags for a regex pattern.
 *
 * This flags may include all or a subset of the pattern modifiers included in php.net documentation.
 *
 * @see https://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
 */
enum RegexFlagsEnum: string
{
    case CASE_INSENSITIVE = 'i';
    case MULTILINE = 'm';
    case SINGLE_LINE = 's';
    case EXTENDED = 'x';
    case UNICODE = 'u';

    /**
     * Returns an associative array of all regex flag characters and their descriptions.
     *
     * @return array<string, string> An array where keys are flag characters and values are their human-readable descriptions.
     */
    public static function getAllFlags(): array
    {
        $flags = [];
        foreach (self::cases() as $flag) {
            $flags[$flag->value] = $flag->getDescription();
        }

        return $flags;
    }

    /**
     * Combines multiple regex flag enums into a single, sorted string of unique flag characters.
     *
     * @param  array<RegexFlagsEnum>  $flags  Array of regex flag enum instances to combine.
     * @return string Concatenated string of unique, alphabetically sorted flag characters.
     */
    public static function combineFlags(array $flags): string
    {
        $flagString = '';
        foreach ($flags as $flag) {
            if ($flag instanceof self) {
                $flagString .= $flag->value;
            }
        }

        // Remove duplicates and sort for consistency
        $uniqueFlags = array_unique(str_split($flagString));
        sort($uniqueFlags);

        return implode('', $uniqueFlags);
    }

    /**
     * Converts a string of regex flag characters into an array of unique `RegexFlagsEnum` instances.
     *
     * Each character in the input string is matched to a corresponding enum case. Duplicate flags are ignored.
     *
     * @param  string  $flags  String containing regex flag characters.
     * @return array<RegexFlagsEnum> Array of unique enum instances representing the provided flags.
     */
    public static function parseFlags(string $flags): array
    {
        $result = [];
        $flagChars = str_split($flags);

        foreach ($flagChars as $char) {
            foreach (self::cases() as $flag) {
                if ($flag->value === $char && ! in_array($flag, $result, true)) {
                    $result[] = $flag;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Returns a human-readable description of the regex flag represented by this enum case.
     *
     * @return string The description of the regex flag.
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::CASE_INSENSITIVE => 'case insensitive matching',
            self::MULTILINE => 'multiline mode (^ and $ match start/end of lines)',
            self::SINGLE_LINE => 'single line mode (. matches newlines)',
            self::EXTENDED => 'extended syntax (allows comments and whitespace)',
            self::UNICODE => 'unicode mode',
        };
    }
}
