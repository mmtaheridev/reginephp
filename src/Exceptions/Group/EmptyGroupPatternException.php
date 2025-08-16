<?php

namespace Regine\Exceptions\Group;

use InvalidArgumentException;

class EmptyGroupPatternException extends InvalidArgumentException
{
    public function __construct(string $message = 'Group pattern cannot be empty.')
    {
        parent::__construct($message);
    }
}
