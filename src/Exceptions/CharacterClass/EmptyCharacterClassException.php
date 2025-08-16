<?php

namespace Regine\Exceptions\CharacterClass;

use InvalidArgumentException;

class EmptyCharacterClassException extends InvalidArgumentException
{
    public function __construct(string $message = 'Character class cannot be empty.')
    {
        parent::__construct($message);
    }
}
