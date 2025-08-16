<?php

namespace Regine\Exceptions\Literal;

use InvalidArgumentException;

class EmptyLiteralComponentException extends InvalidArgumentException
{
    public function __construct(string $message = 'Literal component cannot be empty.')
    {
        parent::__construct($message);
    }
}
