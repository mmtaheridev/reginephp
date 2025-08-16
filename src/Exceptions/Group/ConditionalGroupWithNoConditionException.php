<?php

namespace Regine\Exceptions\Group;

use InvalidArgumentException;

class ConditionalGroupWithNoConditionException extends InvalidArgumentException
{
    public function __construct(string $message = 'Conditional group requires a condition.')
    {
        parent::__construct($message);
    }
}
