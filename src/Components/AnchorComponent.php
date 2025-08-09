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
 */
class AnchorComponent implements RegexComponent
{
    private AnchorTypesEnum $anchorType;

    /****
     * Initializes the AnchorComponent with a specific anchor type.
     *
     * @param AnchorTypesEnum $anchorType The anchor type to represent.
     */
    public function __construct(AnchorTypesEnum $anchorType)
    {
        $this->anchorType = $anchorType;
    }

    /**
     * Creates an AnchorComponent representing the start of a string anchor (`^`).
     *
     * @return self Instance configured for the start of string anchor.
     */
    public static function startOfString(): self
    {
        return new self(AnchorTypesEnum::START_OF_STRING);
    }

    /**
     * Creates an anchor component representing the end of a string (`$`).
     *
     * @return self An instance configured for the end of string anchor.
     */
    public static function endOfString(): self
    {
        return new self(AnchorTypesEnum::END_OF_STRING);
    }

    /**
     * Creates an AnchorComponent representing the start of a line anchor (`^`).
     *
     * @return self Instance configured for the start of line anchor.
     */
    public static function startOfLine(): self
    {
        return new self(AnchorTypesEnum::START_OF_LINE);
    }

    /**
     * Creates an instance representing the end of line anchor (`$`).
     *
     * @return self AnchorComponent instance for the end of line anchor.
     */
    public static function endOfLine(): self
    {
        return new self(AnchorTypesEnum::END_OF_LINE);
    }

    /**
     * Creates an AnchorComponent representing a word boundary anchor (`\b`).
     *
     * @return self Instance configured for the word boundary anchor.
     */
    public static function wordBoundary(): self
    {
        return new self(AnchorTypesEnum::WORD_BOUNDARY);
    }

    /**
     * Creates an AnchorComponent representing a non-word boundary anchor (`\B`).
     *
     * @return self Instance representing the non-word boundary anchor.
     */
    public static function nonWordBoundary(): self
    {
        return new self(AnchorTypesEnum::NON_WORD_BOUNDARY);
    }

    /**
     * Returns the regex string representation of the anchor type.
     *
     * @return string The regex pattern for the specified anchor.
     */
    public function compile(): string
    {
        return $this->anchorType->getRegex();
    }

    /**
     * Returns the string value representing the type of the anchor.
     *
     * @return string The anchor type as a string.
     */
    public function getType(): string
    {
        return $this->anchorType->value;
    }

    /**
     * Returns metadata describing the anchor component.
     *
     * The returned array includes the anchor type, its regex representation, and the enum name.
     *
     * @return array<string, mixed> Associative array with keys 'type', 'anchor', and 'enum'.
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
     * Determines whether the anchor component can be quantified in a regex pattern.
     *
     * @return bool Always returns false, as regex anchors cannot be quantified.
     */
    public function canBeQuantified(): bool
    {
        return false; // Anchors cannot be quantified
    }

    /**
     * Returns a human-readable description of the anchor type represented by this component.
     *
     * @return string The description of the anchor type.
     */
    public function getDescription(): string
    {
        return $this->anchorType->getDescription();
    }
}
