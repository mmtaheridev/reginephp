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
    
    public function anyChar(): self
    {
        $this->pattern .= '.';
        return $this;
    }

    public function digit(): self
    {
        $this->pattern .= '\d';
        return $this;
    }

    public function nonDigit(): self
    {
        $this->pattern .= '\D';
        return $this;
    }

    public function wordChar(): self
    {
        $this->pattern .= '\w';
        return $this;
    }

    public function nonWordChar(): self
    {
        $this->pattern .= '\W';
        return $this;
    }

    public function whitespace(): self
    {
        $this->pattern .= '\s';
        return $this;
    }

    public function nonWhitespace(): self
    {
        $this->pattern .= '\S';
        return $this;
    }
}
