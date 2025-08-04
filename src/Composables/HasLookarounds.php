<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\LookaroundComponent;
use Regine\Regine;

trait HasLookarounds
{
    /**
     * Add a positive lookahead assertion
     *
     * Matches if the pattern appears ahead of the current position,
     * but does not consume the characters.
     *
     * <code>
     *      $regine = Regine::make()->literal('foo')->lookahead('bar'); // foo(?=bar)
     *      $regine = Regine::make()->lookahead(Regine::make()->digit()->oneOrMore()); // (?=\d+)
     * </code>
     *
     * @param  Regine|string  $pattern  The pattern to look ahead for
     */
    public function lookahead(Regine|string $pattern): self
    {
        $component = LookaroundComponent::positiveLookahead($pattern);
        $this->components->add($component);

        return $this;
    }

    /**
     * Add a negative lookahead assertion
     *
     * Matches if the pattern does NOT appear ahead of the current position,
     * but does not consume the characters.
     *
     * <code>
     *      $regine = Regine::make()->literal('foo')->negativeLookahead('bar'); // foo(?!bar)
     *      $regine = Regine::make()->negativeLookahead(Regine::make()->digit()->oneOrMore()); // (?!\d+)
     * </code>
     *
     * @param  Regine|string  $pattern  The pattern to look ahead for (negative)
     */
    public function negativeLookahead(Regine|string $pattern): self
    {
        $component = LookaroundComponent::negativeLookahead($pattern);
        $this->components->add($component);

        return $this;
    }

    /**
     * Add a positive lookbehind assertion
     *
     * Matches if the pattern appears behind the current position,
     * but does not consume the characters.
     *
     * <code>
     *      $regine = Regine::make()->lookbehind('foo')->literal('bar'); // (?<=foo)bar
     *      $regine = Regine::make()->lookbehind(Regine::make()->digit()->oneOrMore()); // (?<=\d+)
     * </code>
     *
     * @param  Regine|string  $pattern  The pattern to look behind for
     */
    public function lookbehind(Regine|string $pattern): self
    {
        $component = LookaroundComponent::positiveLookbehind($pattern);
        $this->components->add($component);

        return $this;
    }

    /**
     * Add a negative lookbehind assertion
     *
     * Matches if the pattern does NOT appear behind the current position,
     * but does not consume the characters.
     *
     * <code>
     *      $regine = Regine::make()->negativeLookbehind('foo')->literal('bar'); // (?<!foo)bar
     *      $regine = Regine::make()->negativeLookbehind(Regine::make()->digit()->oneOrMore()); // (?<!\d+)
     * </code>
     *
     * @param  Regine|string  $pattern  The pattern to look behind for (negative)
     */
    public function negativeLookbehind(Regine|string $pattern): self
    {
        $component = LookaroundComponent::negativeLookbehind($pattern);
        $this->components->add($component);

        return $this;
    }
} 