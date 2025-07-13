<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\LiteralComponent;

trait HasLiterals
{
    public function literal(string $text): self
    {
        $this->components->add(new LiteralComponent($text));

        return $this;
    }
}
