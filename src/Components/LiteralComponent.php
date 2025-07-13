<?php

declare(strict_types=1);

namespace Regine\Components;

use InvalidArgumentException;
use Regine\Contracts\RegexComponent;

class LiteralComponent implements RegexComponent
{
    private static string $type = 'LITERAL';

    private string $text;

    public function __construct(string $text)
    {
        if (empty($text)) {
            throw new InvalidArgumentException('Literal text cannot be empty.');
        }
        $this->text = $text;
    }

    public function compile(): string
    {
        return preg_quote($this->text, '/');
    }

    public function getType(): string
    {
        return 'literal';
    }

    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'text' => $this->text,
            'length' => strlen($this->text),
            'enum' => self::$type, // LiteralComponent doesn't use enums, but adding for consistency
        ];
    }

    public function canBeQuantified(): bool
    {
        return true;
    }

    public function getDescription(): string
    {
        return "match literally '{$this->text}'";
    }
}
