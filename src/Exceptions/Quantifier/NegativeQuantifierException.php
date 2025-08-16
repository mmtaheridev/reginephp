<?php

namespace Regine\Exceptions\Quantifier;

use InvalidArgumentException;

class NegativeQuantifierException extends InvalidArgumentException
{
    public function __construct(string $message = 'Quantifier count must be non-negative.')
    {
        parent::__construct($message);
    }
}
