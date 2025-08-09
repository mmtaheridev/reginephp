<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\LookaroundComponent;
use Regine\Regine;

trait HasLookarounds
{
    /**
     * Adds a positive lookahead assertion to the regex pattern.
     *
     * The resulting pattern matches if the specified pattern appears ahead of the current position, without consuming any characters. Enables chaining for building complex regular expressions.
     *
     * @param  Regine|string  $pattern  The pattern to assert must appear ahead.
     */
    public function lookahead(Regine|string $pattern): self
    {
        $component = LookaroundComponent::positiveLookahead($pattern);
        $this->components->add($component);

        return $this;
    }

    /**
     * Adds a negative lookahead assertion to the regex pattern.
     *
     * The resulting pattern matches if the specified pattern does not appear ahead of the current position, without consuming any characters. Enables chaining for building complex regular expressions.
     *
     * @param  Regine|string  $pattern  The pattern that must not appear ahead of the current position.
     */
    public function negativeLookahead(Regine|string $pattern): self
    {
        $component = LookaroundComponent::negativeLookahead($pattern);
        $this->components->add($component);

        return $this;
    }

    /**
     * Adds a positive lookbehind assertion to the regex pattern.
     *
     * The resulting pattern matches if the specified pattern appears immediately before the current position, without consuming any characters.
     *
     * @param  Regine|string  $pattern  The pattern to assert behind the current position.
     */
    public function lookbehind(Regine|string $pattern): self
    {
        $component = LookaroundComponent::positiveLookbehind($pattern);
        $this->components->add($component);

        return $this;
    }

    /**
     * Adds a negative lookbehind assertion to the regex pattern.
     *
     * The resulting pattern matches only if the specified pattern does not appear immediately before the current position, without consuming any characters.
     *
     * @param  Regine|string  $pattern  The pattern that must not appear behind the current position.
     */
    public function negativeLookbehind(Regine|string $pattern): self
    {
        $component = LookaroundComponent::negativeLookbehind($pattern);
        $this->components->add($component);

        return $this;
    }
}
