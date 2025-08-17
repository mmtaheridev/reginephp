<?php

declare(strict_types=1);

namespace Regine\Contracts;

/**
 * Interface for regex decorators that modify or wrap other elements.
 *
 * Decorators are elements that enhance, modify, or wrap other regex elements.
 * Examples include groups, quantifiers, and lookarounds.
 */
interface RegexDecorator extends RegexElement
{
    /**
     * Get the element that this decorator wraps.
     */
    public function getWrappedElement(): RegexElement;

    /**
     * Check if this decorator can itself be decorated by another decorator.
     */
    public function canBeDecorated(): bool;

    /**
     * Get the decorator precedence level for proper chaining order.
     * Lower numbers have higher precedence (applied first).
     */
    public function getPrecedence(): int;

    /**
     * Check if this decorator needs to wrap its content in a non-capturing group.
     */
    public function needsGrouping(): bool;
}
