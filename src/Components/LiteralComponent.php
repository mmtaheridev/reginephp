<?php

declare(strict_types=1);

namespace Regine\Components;

use InvalidArgumentException;
use Regine\Contracts\RegexComponent;
use Regine\ValueObjects\SafeCharacter;
use Regine\ValueObjects\SafeString;

class LiteralComponent implements RegexComponent
{
    private static string $type = 'LITERAL';

    private SafeString $text;

    public function __construct(string $text)
    {
        if ($text === '') {
            throw new InvalidArgumentException('Literal text cannot be empty.');
        }
        $this->text = SafeString::from($text);
    }

    public function compile(): string
    {
        return $this->text->escaped();
    }

    public function getType(): string
    {
        return self::$type;
    }

    public function getMetadata(): array
    {
        return [
            'type' => self::$type,
            'text' => $this->text->getRaw(),
            'hasSpecialCharacters' => $this->text->hasSpecialCharacters(),
            'specialCharacters' => array_map(
                fn (SafeCharacter $char): string => $char->getRaw(),
                $this->text->getSpecialCharacters()
            ),
        ];
    }

    public function canBeQuantified(): bool
    {
        return true;
    }

    public function getDescription(): string
    {
        return "Literal text: '{$this->text->getRaw()}'";
    }
}
