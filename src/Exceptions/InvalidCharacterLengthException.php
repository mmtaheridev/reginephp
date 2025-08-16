<?php

namespace Regine\Exceptions;

use InvalidArgumentException;

class InvalidCharacterLengthException extends InvalidArgumentException
{
    public function __construct(string $message = 'SafeCharacter can only wrap single characters')
    {
        parent::__construct($message);
    }
}
