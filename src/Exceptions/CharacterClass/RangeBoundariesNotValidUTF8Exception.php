<?php

namespace Regine\Exceptions\CharacterClass;

use InvalidArgumentException;

class RangeBoundariesNotValidUTF8Exception extends InvalidArgumentException
{
    public function __construct(string $message = 'Range boundaries must be valid UTF-8 characters.')
    {
        parent::__construct($message);
    }
}
