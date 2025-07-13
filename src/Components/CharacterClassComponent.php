<?php

declare(strict_types=1);

namespace Regine\Components;

use InvalidArgumentException;
use Regine\Contracts\RegexComponent;
use Regine\Enums\CharacterClassTypesEnum;

class CharacterClassComponent implements RegexComponent
{
    private string $chars;
    private bool $negated;
    private CharacterClassTypesEnum $type;

    public function __construct(string $chars, bool $negated, CharacterClassTypesEnum $type)
    {
        if (empty($chars)) {
            throw new InvalidArgumentException('Characters cannot be empty.');
        }
        $this->chars = $chars;
        $this->negated = $negated;
        $this->type = $type;
    }

    public static function anyOf(string $chars): self
    {
        return new self($chars, false, CharacterClassTypesEnum::ANY_OF);
    }

    public static function noneOf(string $chars): self
    {
        return new self($chars, true, CharacterClassTypesEnum::NONE_OF);
    }

    public static function range(string $from, string $to): self
    {
        if (strlen($from) !== 1 || strlen($to) !== 1) {
            throw new InvalidArgumentException('Range must be single characters.');
        }

        if (ord($from) > ord($to)) {
            throw new InvalidArgumentException('Range start must be less than or equal to range end.');
        }

        return new self($from . '-' . $to, false, CharacterClassTypesEnum::RANGE);
    }

    public function compile(): string
    {
        if ($this->type === CharacterClassTypesEnum::RANGE) {
            // For ranges, don't escape the dash
            [$from, $to] = explode('-', $this->chars);
            $escapedFrom = $this->escapeCharacterClass($from);
            $escapedTo = $this->escapeCharacterClass($to);
            $escaped = $escapedFrom . '-' . $escapedTo;
        } else {
            $escaped = $this->escapeCharacterClass($this->chars);
        }

        $negation = $this->negated ? '^' : '';

        return '[' . $negation . $escaped . ']';
    }

    public function getType(): string
    {
        return $this->type->value;
    }

    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'chars' => $this->chars,
            'negated' => $this->negated,
            'char_count' => strlen($this->chars),
            'enum' => $this->type->name,
        ];
    }

    public function canBeQuantified(): bool
    {
        return true;
    }

    public function getDescription(): string
    {
        if ($this->type === CharacterClassTypesEnum::RANGE) {
            [$from, $to] = explode('-', $this->chars);

            return "match character range from '{$from}' to '{$to}'";
        }

        $operation = $this->negated ? 'match none of' : $this->type->getDescription();

        return "{$operation} '{$this->chars}'";
    }

    /**
     * Escape special characters within character classes
     */
    private function escapeCharacterClass(string $chars): string
    {
        return strtr($chars, [
            '\\' => '\\\\',
            ']' => '\\]',
            '^' => '\\^',
            '-' => '\\-',
        ]);
    }
}
