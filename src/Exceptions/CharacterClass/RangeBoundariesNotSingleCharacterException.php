<?php

namespace Regine\Exceptions\CharacterClass;

use InvalidArgumentException;

class RangeBoundariesNotSingleCharacterException extends InvalidArgumentException
{
    public function __construct(string $message = 'Range boundaries must be single characters.')
    {
        parent::__construct($message);
    }
}
