<?php

namespace Regine\Exceptions\CharacterClass;

use InvalidArgumentException;

class RangeBoundariesAreNotSetException extends InvalidArgumentException
{
    public function __construct(string $message = 'Range type requires both fromChar and toChar.')
    {
        parent::__construct($message);
    }
}
