<?php

namespace Regine\Exceptions\Alternation;

use InvalidArgumentException;

class EmptyAlternationException extends InvalidArgumentException
{
    public function __construct(string $message = 'Alternation requires at least one alternative.')
    {
        parent::__construct($message);
    }
}
