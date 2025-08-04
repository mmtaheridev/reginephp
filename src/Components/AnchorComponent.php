<?php

declare(strict_types=1);

namespace Regine\Components;

use Regine\Contracts\RegexComponent;
use Regine\Enums\AnchorTypesEnum;

/**
 * Anchor component
 * 
 * Implements a regex component that represents an anchor
 * (start of string, end of string, start of line, end of line, word boundary, non-word boundary)
 * 
 */
class AnchorComponent implements RegexComponent
{
    private AnchorTypesEnum $anchorType;

    /**
     * Create an anchor component
     */
    public function __construct(AnchorTypesEnum $anchorType)
    {
        $this->anchorType = $anchorType;
    }

    /**
     * Create a start of string anchor (^)
     */
    public static function startOfString(): self
    {
        return new self(AnchorTypesEnum::START_OF_STRING);
    }

    /**
     * Create an end of string anchor ($)
     */
    public static function endOfString(): self
    {
        return new self(AnchorTypesEnum::END_OF_STRING);
    }

    /**
     * Create a start of line anchor (^)
     */
    public static function startOfLine(): self
    {
        return new self(AnchorTypesEnum::START_OF_LINE);
    }

    /**
     * Create an end of line anchor ($)
     */
    public static function endOfLine(): self
    {
        return new self(AnchorTypesEnum::END_OF_LINE);
    }

    /**
     * Create a word boundary anchor (\b)
     */
    public static function wordBoundary(): self
    {
        return new self(AnchorTypesEnum::WORD_BOUNDARY);
    }

    /**
     * Create a non-word boundary anchor (\B)
     */
    public static function nonWordBoundary(): self
    {
        return new self(AnchorTypesEnum::NON_WORD_BOUNDARY);
    }

    /**
     * Compile the anchor component into a regex string
     */
    public function compile(): string
    {
        return $this->anchorType->getRegex();
    }

    /**
     * Get the type of the anchor component
     */
    public function getType(): string
    {
        return $this->anchorType->value;
    }

    /**
     * Get metadata about the anchor component
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'anchor' => $this->anchorType->getRegex(),
            'enum' => $this->anchorType->name,
        ];
    }

    /**
     * Check if the anchor component can be quantified
     */
    public function canBeQuantified(): bool
    {
        return false; // Anchors cannot be quantified
    }

    /**
     * Get a human-readable description of the anchor component
     */
    public function getDescription(): string
    {
        return $this->anchorType->getDescription();
    }
}
