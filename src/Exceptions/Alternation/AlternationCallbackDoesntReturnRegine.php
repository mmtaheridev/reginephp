<?php

namespace Regine\Exceptions\Alternation;

use InvalidArgumentException;

class AlternationCallbackDoesntReturnRegine extends InvalidArgumentException
{
    public function __construct(string $message = 'Alternation callback must return a Regine instance.')
    {
        parent::__construct($message);
    }
}
