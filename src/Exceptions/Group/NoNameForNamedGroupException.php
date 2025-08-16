<?php

namespace Regine\Exceptions\Group;

use InvalidArgumentException;

class NoNameForNamedGroupException extends InvalidArgumentException
{
    public function __construct(string $message = 'Named group requires a name.')
    {
        parent::__construct($message);
    }
}
