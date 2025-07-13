<?php

declare(strict_types=1);

namespace Regine\Components;

use Regine\Contracts\RegexComponent;
use Regine\Enums\ShorthandTypesEnum;

class ShorthandComponent implements RegexComponent
{
    private ShorthandTypesEnum $shorthandType;

    public function __construct(ShorthandTypesEnum $shorthandType)
    {
        $this->shorthandType = $shorthandType;
    }

    public static function anyChar(): self
    {
        return new self(ShorthandTypesEnum::ANY_CHAR);
    }

    public static function digit(): self
    {
        return new self(ShorthandTypesEnum::DIGIT);
    }

    public static function nonDigit(): self
    {
        return new self(ShorthandTypesEnum::NON_DIGIT);
    }

    public static function wordChar(): self
    {
        return new self(ShorthandTypesEnum::WORD_CHAR);
    }

    public static function nonWordChar(): self
    {
        return new self(ShorthandTypesEnum::NON_WORD_CHAR);
    }

    public static function whitespace(): self
    {
        return new self(ShorthandTypesEnum::WHITESPACE);
    }

    public static function nonWhitespace(): self
    {
        return new self(ShorthandTypesEnum::NON_WHITESPACE);
    }

    public static function letter(): self
    {
        return new self(ShorthandTypesEnum::LETTER);
    }

    public function compile(): string
    {
        return $this->shorthandType->getRegex();
    }

    public function getType(): string
    {
        return $this->shorthandType->value;
    }

    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'shorthand' => $this->shorthandType->getRegex(),
            'description' => $this->getDescription(),
            'enum' => $this->shorthandType->name,
        ];
    }

    public function canBeQuantified(): bool
    {
        return true;
    }

    public function getDescription(): string
    {
        return $this->shorthandType->getDescription();
    }
}
