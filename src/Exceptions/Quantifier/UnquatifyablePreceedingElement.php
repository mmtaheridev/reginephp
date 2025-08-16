<?php

namespace Regine\Exceptions\Quantifier;

use InvalidArgumentException;

class UnquatifyablePreceedingElement extends InvalidArgumentException
{
    public function __construct(string $message = 'Cannot add quantifier: preceding element cannot be quantified.')
    {
        parent::__construct($message);
    }
}
