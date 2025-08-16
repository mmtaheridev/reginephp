<?php

namespace Regine\Exceptions\Group;

use InvalidArgumentException;

class NameForUnnamedGroupException extends InvalidArgumentException
{
    public function __construct(string $message = 'Only named groups can have a name.')
    {
        parent::__construct($message);
    }
}
