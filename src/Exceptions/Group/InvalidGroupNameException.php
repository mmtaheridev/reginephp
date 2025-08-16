<?php

namespace Regine\Exceptions\Group;

use InvalidArgumentException;

class InvalidGroupNameException extends InvalidArgumentException
{
    public function __construct(string $message = 'Invalid group name. Must contain only letters, digits, and underscores, and cannot start with a digit.')
    {
        parent::__construct($message);
    }
}
