<?php

declare(strict_types=1);

namespace Regine\Composables;

use LogicException;
use Regine\Components\LiteralComponent;
use Regine\Components\RawPatternComponent;
use Regine\Contracts\RegexElement;
use Regine\Decorators\LookaroundDecorator;
use Regine\Enums\LookaroundTypesEnum;
use Regine\Regine;

trait HasLookarounds
{
    /**
     * Adds a positive lookahead assertion to the regex pattern.
     *
     * The resulting pattern matches if the specified pattern appears ahead of the current position,
     * without consuming any characters. Uses the decorator pattern with an empty element.
     *
     * @param  Regine|string  $pattern  The pattern to assert must appear ahead.
     */
    public function lookahead(Regine|string $pattern): self
    {
        $assertionContent = $this->createLookaroundElementFromPattern($pattern);
        $emptyElement = new LiteralComponent('(?:)'); // Zero-width assertion base

        $lookaroundDecorator = new LookaroundDecorator(
            element: $emptyElement,
            lookaroundType: LookaroundTypesEnum::POSITIVE_LOOKAHEAD,
            assertionContent: $assertionContent
        );

        $this->elements->add($lookaroundDecorator);

        return $this;
    }

    /**
     * Adds a negative lookahead assertion to the regex pattern.
     *
     * The resulting pattern matches if the specified pattern does not appear ahead of the current position,
     * without consuming any characters.
     *
     * @param  Regine|string  $pattern  The pattern that must not appear ahead of the current position.
     */
    public function negativeLookahead(Regine|string $pattern): self
    {
        $assertionContent = $this->createLookaroundElementFromPattern($pattern);
        $emptyElement = new LiteralComponent('(?:)');

        $lookaroundDecorator = new LookaroundDecorator(
            element: $emptyElement,
            lookaroundType: LookaroundTypesEnum::NEGATIVE_LOOKAHEAD,
            assertionContent: $assertionContent
        );

        $this->elements->add($lookaroundDecorator);

        return $this;
    }

    /**
     * Adds a positive lookbehind assertion to the regex pattern.
     *
     * The resulting pattern matches if the specified pattern appears immediately before the current position,
     * without consuming any characters.
     *
     * @param  Regine|string  $pattern  The pattern to assert behind the current position.
     */
    public function lookbehind(Regine|string $pattern): self
    {
        $assertionContent = $this->createLookaroundElementFromPattern($pattern);
        $emptyElement = new LiteralComponent('(?:)');

        $lookaroundDecorator = new LookaroundDecorator(
            element: $emptyElement,
            lookaroundType: LookaroundTypesEnum::POSITIVE_LOOKBEHIND,
            assertionContent: $assertionContent
        );

        $this->elements->add($lookaroundDecorator);

        return $this;
    }

    /**
     * Adds a negative lookbehind assertion to the regex pattern.
     *
     * The resulting pattern matches only if the specified pattern does not appear immediately before
     * the current position, without consuming any characters.
     *
     * @param  Regine|string  $pattern  The pattern that must not appear behind the current position.
     */
    public function negativeLookbehind(Regine|string $pattern): self
    {
        $assertionContent = $this->createLookaroundElementFromPattern($pattern);
        $emptyElement = new LiteralComponent('(?:)');

        $lookaroundDecorator = new LookaroundDecorator(
            element: $emptyElement,
            lookaroundType: LookaroundTypesEnum::NEGATIVE_LOOKBEHIND,
            assertionContent: $assertionContent
        );

        $this->elements->add($lookaroundDecorator);

        return $this;
    }

    /**
     * Wrap the last element with a positive lookahead assertion.
     *
     * This is the true decorator approach - it takes the last element and wraps it
     * with a lookahead assertion that must match the given pattern.
     */
    public function withLookahead(Regine|string $pattern): self
    {
        $this->wrapLastElementWithLookaround(LookaroundTypesEnum::POSITIVE_LOOKAHEAD, $pattern);

        return $this;
    }

    /**
     * Wrap the last element with a negative lookahead assertion.
     */
    public function withNegativeLookahead(Regine|string $pattern): self
    {
        $this->wrapLastElementWithLookaround(LookaroundTypesEnum::NEGATIVE_LOOKAHEAD, $pattern);

        return $this;
    }

    /**
     * Wrap the last element with a positive lookbehind assertion.
     */
    public function withLookbehind(Regine|string $pattern): self
    {
        $this->wrapLastElementWithLookaround(LookaroundTypesEnum::POSITIVE_LOOKBEHIND, $pattern);

        return $this;
    }

    /**
     * Wrap the last element with a negative lookbehind assertion.
     */
    public function withNegativeLookbehind(Regine|string $pattern): self
    {
        $this->wrapLastElementWithLookaround(LookaroundTypesEnum::NEGATIVE_LOOKBEHIND, $pattern);

        return $this;
    }

    /**
     * Helper method to wrap the last element with a lookaround decorator.
     */
    private function wrapLastElementWithLookaround(LookaroundTypesEnum $type, Regine|string $pattern): void
    {
        $lastElement = $this->elements->getLastElement();

        if ($lastElement === null) {
            throw new LogicException('Cannot wrap with lookaround: no preceding element');
        }

        $assertionContent = $this->createLookaroundElementFromPattern($pattern);

        // Remove the last element and replace it with a lookaround-decorated version
        $this->elements->removeLast();

        $lookaroundDecorator = new LookaroundDecorator(
            element: $lastElement,
            lookaroundType: $type,
            assertionContent: $assertionContent
        );
        $this->elements->add($lookaroundDecorator);
    }

    /**
     * Convert a pattern (string or Regine) to a regex element.
     */
    private function createLookaroundElementFromPattern(Regine|string $pattern): RegexElement
    {
        if ($pattern instanceof Regine) {
            // For Regine objects, use raw pattern component to avoid double-escaping
            return new RawPatternComponent($pattern->compileRaw());
        }

        // For strings in lookarounds, treat them as raw regex patterns
        // Users expect patterns like '.*[a-z]' to work as regex, not be escaped as literals
        return new RawPatternComponent($pattern);
    }
}
