<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\CharacterClassComponent;

trait HasCharacterClasses
{
    public function anyOf(string $chars): self
    {
        $this->components->add(CharacterClassComponent::anyOf($chars));

        return $this;
    }

    public function noneOf(string $chars): self
    {
        $this->components->add(CharacterClassComponent::noneOf($chars));

        return $this;
    }

    public function range(string $from, string $to): self
    {
        $this->components->add(CharacterClassComponent::range($from, $to));

        return $this;
    }
}
