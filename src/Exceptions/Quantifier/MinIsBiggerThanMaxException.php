<?php

namespace Regine\Exceptions\Quantifier;

use InvalidArgumentException;

class MinIsBiggerThanMaxException extends InvalidArgumentException
{
    public function __construct(int $min, int $max)
    {
        parent::__construct(
            "Minimum count must be less than or equal to maximum count. (min: {$min}, max: {$max})"
        );
    }
}
