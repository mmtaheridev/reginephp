<?php

declare(strict_types=1);

namespace Regine\Components;

use Regine\Contracts\RegexComponent;
use Regine\Enums\AnchorTypesEnum;

class AnchorComponent implements RegexComponent
{
    private AnchorTypesEnum $anchorType;

    public function __construct(AnchorTypesEnum $anchorType)
    {
        $this->anchorType = $anchorType;
    }

    public static function startOfString(): self
    {
        return new self(AnchorTypesEnum::START_OF_STRING);
    }

    public static function endOfString(): self
    {
        return new self(AnchorTypesEnum::END_OF_STRING);
    }

    public static function startOfLine(): self
    {
        return new self(AnchorTypesEnum::START_OF_LINE);
    }

    public static function endOfLine(): self
    {
        return new self(AnchorTypesEnum::END_OF_LINE);
    }

    public static function wordBoundary(): self
    {
        return new self(AnchorTypesEnum::WORD_BOUNDARY);
    }

    public static function nonWordBoundary(): self
    {
        return new self(AnchorTypesEnum::NON_WORD_BOUNDARY);
    }

    public function compile(): string
    {
        return $this->anchorType->getRegex();
    }

    public function getType(): string
    {
        return $this->anchorType->value;
    }

    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'anchor' => $this->anchorType->getRegex(),
            'enum' => $this->anchorType->name,
        ];
    }

    public function canBeQuantified(): bool
    {
        return false; // Anchors cannot be quantified
    }

    public function getDescription(): string
    {
        return $this->anchorType->getDescription();
    }
}
