<?php

namespace Regine\Exceptions\CharacterClass;

use InvalidArgumentException;

class RangeStartGreaterThanEndException extends InvalidArgumentException
{
    public function __construct(string $message = 'Range start must be less than or equal to range end.')
    {
        parent::__construct($message);
    }
}
