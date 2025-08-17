<?php

namespace Regine\Exceptions\Quantifier;

use InvalidArgumentException;

class QuantifierForNoPreceedingElementException extends InvalidArgumentException
{
    public function __construct(string $message = 'Cannot add quantifier: no preceding element.')
    {
        parent::__construct($message);
    }
}
