<?php

declare(strict_types=1);

namespace Regine\Components;

use Regine\Contracts\RegexComponent;
use Regine\Enums\ComponentType;

/**
 * A component that represents an already-compiled regex pattern.
 *
 * Unlike LiteralComponent, this doesn't escape the content since it's
 * assumed to be already properly formatted regex pattern.
 */
class RawPatternComponent implements RegexComponent
{
    private string $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function compile(): string
    {
        return $this->pattern;
    }

    public function getType(): string
    {
        return ComponentType::RAW_PATTERN->value;
    }

    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'pattern' => $this->pattern,
        ];
    }

    public function canBeQuantified(): bool
    {
        return true;
    }

    public function getDescription(): string
    {
        return "Raw pattern: '{$this->pattern}'";
    }
}
