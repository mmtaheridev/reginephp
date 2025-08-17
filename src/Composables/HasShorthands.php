<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\ShorthandComponent;

trait HasShorthands
{
    public function anyChar(): self
    {
        $this->elements->add(ShorthandComponent::anyChar());

        return $this;
    }

    public function digit(): self
    {
        $this->elements->add(ShorthandComponent::digit());

        return $this;
    }

    public function nonDigit(): self
    {
        $this->elements->add(ShorthandComponent::nonDigit());

        return $this;
    }

    public function wordChar(): self
    {
        $this->elements->add(ShorthandComponent::wordChar());

        return $this;
    }

    public function nonWordChar(): self
    {
        $this->elements->add(ShorthandComponent::nonWordChar());

        return $this;
    }

    public function whitespace(): self
    {
        $this->elements->add(ShorthandComponent::whitespace());

        return $this;
    }

    public function nonWhitespace(): self
    {
        $this->elements->add(ShorthandComponent::nonWhitespace());

        return $this;
    }

    /**
     * Match any letter (uppercase or lowercase)
     */
    public function letter(): self
    {
        $this->elements->add(ShorthandComponent::letter());

        return $this;
    }
}
