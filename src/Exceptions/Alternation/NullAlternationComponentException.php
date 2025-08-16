<?php

namespace Regine\Exceptions\Alternation;

use RuntimeException;

class NullAlternationComponentException extends RuntimeException
{
    public function __construct(string $message = 'Expected alternation component but found null.')
    {
        parent::__construct($message);
    }
}
