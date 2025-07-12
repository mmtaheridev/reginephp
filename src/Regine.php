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

    /**
     * Match any letter (uppercase or lowercase)
     */
    public function letter(): self
    {
        $this->pattern .= '[a-zA-Z]';
        return $this;
    }

    public function anyOf(string $chars): self
    {
        if (empty($chars)) {
            throw new InvalidArgumentException('Characters cannot be empty.');
        }

        // Escape special characters in character class context
        $escaped = $this->escapeCharacterClass($chars);
        $this->pattern .= '[' . $escaped . ']';
        return $this;
    }

    public function noneOf(string $chars): self 
    {     
        if (empty($chars)) {         
            throw new InvalidArgumentException('Characters cannot be empty.');     
        }     
        
        // Escape special characters in character class context
        $escaped = $this->escapeCharacterClass($chars);
        $this->pattern .= '[^' . $escaped . ']';     
        return $this; 
    }  
    
    public function range(string $from, string $to): self 
    {     
        if (strlen($from) !== 1 || strlen($to) !== 1) {         
            throw new InvalidArgumentException('Range must be single characters.');     
        }
        
        if (ord($from) > ord($to)) {
            throw new InvalidArgumentException('Range start must be less than or equal to range end.');
        }
        
        // For ranges, we need to escape the characters but not the dash
        $escapedFrom = $this->escapeCharacterClass($from);
        $escapedTo = $this->escapeCharacterClass($to);
        $this->pattern .= '[' . $escapedFrom . '-' . $escapedTo . ']';
        
        return $this; 
    }

    /**
     * Escape special characters within character classes
     */
    protected function escapeCharacterClass(string $chars): string
    {
        return strtr($chars, [
            '\\' => '\\\\',
            ']' => '\\]',
            '^' => '\\^',
            '-' => '\\-'
        ]);
    }

    /**
     * Match zero or more occurrences of the preceding element
     */
    public function zeroOrMore(): self
    {
        $this->pattern .= '*';
        return $this;
    }

    /**
     * Match one or more occurrences of the preceding element
     */
    public function oneOrMore(): self
    {
        $this->pattern .= '+';
        return $this;
    }

    /**
     * Match zero or one occurrence of the preceding element
     */
    public function optional(): self
    {
        $this->pattern .= '?';
        return $this;
    }

    /**
     * Match exactly n occurrences of the preceding element
     */
    public function exactly(int $n): self
    {
        if ($n < 0) {
            throw new InvalidArgumentException('Quantifier count must be non-negative.');
        }
        
        $this->pattern .= '{' . $n . '}';
        return $this;
    }

    /**
     * Match at least n occurrences of the preceding element
     */
    public function atLeast(int $n): self
    {
        if ($n < 0) {
            throw new InvalidArgumentException('Quantifier count must be non-negative.');
        }
        
        $this->pattern .= '{' . $n . ',}';
        return $this;
    }

    /**
     * Match between min and max occurrences of the preceding element
     */
    public function between(int $min, int $max): self
    {
        if ($min < 0 || $max < 0) {
            throw new InvalidArgumentException('Quantifier counts must be non-negative.');
        }
        
        if ($min > $max) {
            throw new InvalidArgumentException('Minimum count must be less than or equal to maximum count.');
        }
        
        $this->pattern .= '{' . $min . ',' . $max . '}';
        return $this;
    }

    /**
     * Match the start of the string
     */
    public function startOfString(): self
    {
        $this->pattern .= '^';
        return $this;
    }

    /**
     * Match the end of the string
     */
    public function endOfString(): self
    {
        $this->pattern .= '$';
        return $this;
    }

    /**
     * Match the start of a line (same as startOfString unless multiline flag is used)
     */
    public function startOfLine(): self
    {
        $this->pattern .= '^';
        return $this;
    }

    /**
     * Match the end of a line (same as endOfString unless multiline flag is used)
     */
    public function endOfLine(): self
    {
        $this->pattern .= '$';
        return $this;
    }

    /**
     * Match a word boundary
     */
    public function wordBoundary(): self
    {
        $this->pattern .= '\b';
        return $this;
    }

    /**
     * Match a non-word boundary
     */
    public function nonWordBoundary(): self
    {
        $this->pattern .= '\B';
        return $this;
    }
}
