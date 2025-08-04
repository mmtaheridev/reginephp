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
     * Set specific flags for the regex pattern
     *
     * <code>
     *      $regine = Regine::make()->literal('test')->withFlags(['i', 'm']); // /test/im
     *      $regine = Regine::make()->literal('test')->withFlags([RegexFlagsEnum::CASE_INSENSITIVE]); // /test/i
     * </code>
     *
     * @param  array<string|RegexFlagsEnum>  $flags  Array of flag strings or RegexFlagsEnum instances
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
     * Enable case-insensitive matching
     *
     * <code>
     *      $regine = Regine::make()->literal('Test')->caseInsensitive(); // /Test/i
     * </code>
     */
    public function caseInsensitive(): self
    {
        return $this->addFlag(RegexFlagsEnum::CASE_INSENSITIVE);
    }

    /**
     * Alias for caseInsensitive
     * 
     * @see caseInsensitive
     */
    public function i(): self
    {
        return $this->caseInsensitive();
    }

    /**
     * Enable multiline mode (^ and $ match start/end of lines)
     *
     * <code>
     *      $regine = Regine::make()->startOfString()->literal('test')->multiline(); // /^test/m
     * </code>
     */
    public function multiline(): self
    {
        return $this->addFlag(RegexFlagsEnum::MULTILINE);
    }

    /**
     * Alias for multiline
     * 
     * @see multiline
     */
    public function m(): self
    {
        return $this->multiline();
    }

    /**
     * Enable single line mode (. matches newlines)
     *
     * <code>
     *      $regine = Regine::make()->anyChar()->oneOrMore()->dotAll(); // /.+/s
     * </code>
     */
    public function dotAll(): self
    {
        return $this->addFlag(RegexFlagsEnum::SINGLE_LINE);
    }

    /**
     * Alias for dotAll
     * 
     * @see dotAll
     */
    public function s(): self
    {
        return $this->dotAll();
    }

    /**
     * Enable extended syntax (allows comments and whitespace)
     *
     * <code>
     *      $regine = Regine::make()->literal('test')->extended(); // /test/x
     * </code>
     */
    public function extended(): self
    {
        return $this->addFlag(RegexFlagsEnum::EXTENDED);
    }

    /**
     * Alias for extended
     * 
     * @see extended
     */
    public function x(): self
    {
        return $this->extended();
    }

    /**
     * Enable unicode mode
     *
     * <code>
     *      $regine = Regine::make()->literal('тест')->unicode(); // /тест/u
     * </code>
     */
    public function unicode(): self
    {
        return $this->addFlag(RegexFlagsEnum::UNICODE);
    }

    /**
     * Alias for unicode
     * 
     * @see unicode
     */
    public function u(): self
    {
        return $this->unicode();
    }

    /**
     * Remove a specific flag
     */
    public function removeFlag(RegexFlagsEnum $flag): self
    {
        $this->flags = array_filter($this->flags, fn($f) => $f !== $flag);
        return $this;
    }

    /**
     * Clear all flags
     */
    public function clearFlags(): self
    {
        $this->flags = [];
        return $this;
    }

    /**
     * Check if a specific flag is set
     */
    public function hasFlag(RegexFlagsEnum $flag): bool
    {
        return in_array($flag, $this->flags, true);
    }

    /**
     * Get all current flags as string
     */
    public function getFlagsString(): string
    {
        return RegexFlagsEnum::combineFlags($this->flags);
    }

    /**
     * Get all current flags as array
     *
     * @return array<RegexFlagsEnum>
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    /**
     * Add a flag to the current flags array
     */
    private function addFlag(RegexFlagsEnum $flag): self
    {
        if (!in_array($flag, $this->flags, true)) {
            $this->flags[] = $flag;
        }

        return $this;
    }
}