<?php

namespace Regine;

use InvalidArgumentException;

class Regine
{
    protected string $pattern = '';

    public static function make(): self
    {
        return new self();
    }

    public function literal(string $text): self
    {
        if (empty($text)) {
            throw new InvalidArgumentException('Literal text cannot be empty.');
        }
        $this->pattern .= preg_quote($text, '/');
        return $this;
    }

    public function compile(string $delimiter = '/'): string
    {
        return $delimiter . $this->pattern . $delimiter;
    }
}
