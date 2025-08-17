<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\AnchorComponent;

trait HasAnchors
{
    /**
     * Match the start of the string
     */
    public function startOfString(): self
    {
        $this->elements->add(AnchorComponent::startOfString());

        return $this;
    }

    /**
     * Match the end of the string
     */
    public function endOfString(): self
    {
        $this->elements->add(AnchorComponent::endOfString());

        return $this;
    }

    /**
     * Match the start of a line (same as startOfString unless multiline flag is used)
     */
    public function startOfLine(): self
    {
        $this->elements->add(AnchorComponent::startOfLine());

        return $this;
    }

    /**
     * Match the end of a line (same as endOfString unless multiline flag is used)
     */
    public function endOfLine(): self
    {
        $this->elements->add(AnchorComponent::endOfLine());

        return $this;
    }

    /**
     * Match a word boundary
     */
    public function wordBoundary(): self
    {
        $this->elements->add(AnchorComponent::wordBoundary());

        return $this;
    }

    /**
     * Match a non-word boundary
     */
    public function nonWordBoundary(): self
    {
        $this->elements->add(AnchorComponent::nonWordBoundary());

        return $this;
    }
}
