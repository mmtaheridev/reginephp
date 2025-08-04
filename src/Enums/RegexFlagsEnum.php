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
     * Get a human-readable description of the flag
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

    /**
     * Get all available flags as an array
     *
     * @return array<string, string>
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
     * Combine multiple flags into a string
     *
     * @param  array<RegexFlagsEnum>  $flags
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
     * Parse flags string into array of enums
     *
     * @return array<RegexFlagsEnum>
     */
    public static function parseFlags(string $flags): array
    {
        $result = [];
        $flagChars = str_split($flags);

        foreach ($flagChars as $char) {
            foreach (self::cases() as $flag) {
                if ($flag->value === $char && !in_array($flag, $result, true)) {
                    $result[] = $flag;
                    break;
                }
            }
        }

        return $result;
    }
}