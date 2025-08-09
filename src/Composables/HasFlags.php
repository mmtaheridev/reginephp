<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Enums\RegexFlagsEnum;

/**
 * This trait provides methods to add and manage flags for a Regine object.
 */
trait HasFlags
{
    /** @var array<RegexFlagsEnum> */
    protected array $flags = [];

    /**
     * Replaces all current regex flags with the specified set.
     *
     * Accepts an array of flag strings or RegexFlagsEnum instances, normalizes them, and sets them as the active flags for the regex pattern.
     *
     * @param  array<string|RegexFlagsEnum>  $flags  Array of flag strings or RegexFlagsEnum instances to set.
     */
    public function withFlags(array $flags): self
    {
        $this->flags = [];

        foreach ($flags as $flag) {
            if ($flag instanceof RegexFlagsEnum) {
                $this->addFlag($flag);
            } elseif (is_string($flag)) {
                $parsedFlags = RegexFlagsEnum::parseFlags($flag);
                foreach ($parsedFlags as $parsedFlag) {
                    $this->addFlag($parsedFlag);
                }
            }
        }

        return $this;
    }

    /**
     * Enables case-insensitive regex matching by adding the corresponding flag.
     *
     * @return self The current instance with the case-insensitive flag enabled.
     */
    public function caseInsensitive(): self
    {
        return $this->addFlag(RegexFlagsEnum::CASE_INSENSITIVE);
    }

    /**
     * Enables the case-insensitive regex flag.
     *
     * Alias for the caseInsensitive() method.
     */
    public function i(): self
    {
        return $this->caseInsensitive();
    }

    /**
     * Enables multiline mode, allowing `^` and `$` to match the start and end of lines.
     */
    public function multiline(): self
    {
        return $this->addFlag(RegexFlagsEnum::MULTILINE);
    }

    /**
     * Alias for enabling the multiline regex flag.
     *
     * @see multiline()
     */
    public function m(): self
    {
        return $this->multiline();
    }

    /**
     * Enables single line (dot all) mode, allowing the dot (`.`) to match newline characters in regex patterns.
     */
    public function dotAll(): self
    {
        return $this->addFlag(RegexFlagsEnum::SINGLE_LINE);
    }

    /**
     * Enables the dot-all (single line) regex flag.
     *
     * This is an alias for the dotAll() method.
     *
     * @see dotAll
     */
    public function s(): self
    {
        return $this->dotAll();
    }

    /**
     * Enables the extended regex flag, allowing comments and whitespace in the pattern.
     */
    public function extended(): self
    {
        return $this->addFlag(RegexFlagsEnum::EXTENDED);
    }

    /**
     * Enables the extended regex flag.
     *
     * Alias for the `extended()` method.
     *
     * @see extended
     */
    public function x(): self
    {
        return $this->extended();
    }

    /**
     * Enables the Unicode regex flag.
     *
     * Allows the regular expression to interpret the pattern and subject as UTF-8, enabling Unicode character matching.
     */
    public function unicode(): self
    {
        return $this->addFlag(RegexFlagsEnum::UNICODE);
    }

    /**
     * Enables the Unicode regex flag.
     *
     * Alias for the unicode() method.
     *
     * @see unicode()
     */
    public function u(): self
    {
        return $this->unicode();
    }

    /**
     * Removes the specified regex flag from the current set of flags.
     *
     * @param  RegexFlagsEnum  $flag  The flag to remove.
     */
    public function removeFlag(RegexFlagsEnum $flag): self
    {
        $this->flags = array_filter($this->flags, fn ($f) => $f !== $flag);

        return $this;
    }

    /**
     * Removes all regex flags from the current set.
     *
     * @return self The current instance for method chaining.
     */
    public function clearFlags(): self
    {
        $this->flags = [];

        return $this;
    }

    /**
     * Determines whether a specific regex flag is currently set.
     *
     * @param  RegexFlagsEnum  $flag  The flag to check for.
     * @return bool True if the flag is set; otherwise, false.
     */
    public function hasFlag(RegexFlagsEnum $flag): bool
    {
        return in_array($flag, $this->flags, true);
    }

    /**
     * Returns the current regex flags combined into a single string representation.
     *
     * @return string The combined string of all active regex flags.
     */
    public function getFlagsString(): string
    {
        return RegexFlagsEnum::combineFlags($this->flags);
    }

    /**
     * Returns the current regex flags as an array of `RegexFlagsEnum` instances.
     *
     * @return array<RegexFlagsEnum> The currently set regex flags.
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    /**
     * Adds a regex flag to the current set if it is not already present.
     *
     * @param  RegexFlagsEnum  $flag  The regex flag to add.
     * @return self Returns the current instance for method chaining.
     */
    private function addFlag(RegexFlagsEnum $flag): self
    {
        if (! in_array($flag, $this->flags, true)) {
            $this->flags[] = $flag;
        }

        return $this;
    }
}
