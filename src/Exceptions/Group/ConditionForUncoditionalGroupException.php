<?php

namespace Regine\Exceptions\Group;

use InvalidArgumentException;

class ConditionForUncoditionalGroupException extends InvalidArgumentException
{
    public function __construct(string $message = 'Only conditional groups can have conditions or else patterns.')
    {
        parent::__construct($message);
    }
}
