<?php

declare(strict_types=1);

namespace Regine\Components;

use Regine\Contracts\RegexComponent;
use Regine\Exceptions\Literal\EmptyLiteralComponentException;
use Regine\ValueObjects\SafeCharacter;
use Regine\ValueObjects\SafeString;

class LiteralComponent implements RegexComponent
{
    private static string $type = 'LITERAL';

    private SafeString $text;

    /**
     * Creates a new LiteralComponent with the given text.
     *
     * @param  string  $text  The literal text to represent.
     *
     * @throws EmptyLiteralComponentException If the provided text is an empty string.
     */
    public function __construct(string $text)
    {
        if ($text === '') {
            throw new EmptyLiteralComponentException;
        }
        $this->text = SafeString::from($text);
    }

    /**
     * Returns the regex-escaped version of the literal text.
     *
     * @return string The escaped literal suitable for use in a regular expression.
     */
    public function compile(): string
    {
        return $this->text->escaped();
    }

    /**
     * Returns the type identifier for this regex component.
     *
     * @return string The string 'LITERAL'.
     */
    public function getType(): string
    {
        return self::$type;
    }

    /**
     * Returns metadata about the literal component, including its type, raw text, and information about special regex characters.
     *
     * @return array<string, mixed> Associative array with keys: 'type', 'text', 'hasSpecialCharacters', and 'specialCharacters'.
     */
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

    /**
     * Returns a human-readable description of the literal text component.
     *
     * @return string Description in the format "Literal text: '<raw text>'".
     */
    public function getDescription(): string
    {
        return "Literal text: '{$this->text->getRaw()}'";
    }
}
