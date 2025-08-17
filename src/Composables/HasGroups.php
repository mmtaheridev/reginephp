<?php

declare(strict_types=1);

namespace Regine\Composables;

use LogicException;
use Regine\Components\RawPatternComponent;
use Regine\Contracts\RegexElement;
use Regine\Decorators\GroupDecorator;
use Regine\Enums\GroupTypesEnum;
use Regine\Regine;

trait HasGroups
{
    /**
     * Adds a capturing group to the pattern.
     *
     * Wraps the given pattern in a capturing group, equivalent to `(pattern)` in regular expressions.
     *
     * @param  Regine|string  $pattern  The pattern to include in the capturing group.
     */
    public function group(Regine|string $pattern): self
    {
        $element = $this->createElementFromPattern($pattern);
        $groupDecorator = new GroupDecorator(
            element: $element,
            groupType: GroupTypesEnum::CAPTURING
        );

        $this->elements->add($groupDecorator);

        return $this;
    }

    /**
     * Adds a non-capturing group to the regex pattern.
     *
     * Wraps the given pattern in a non-capturing group `(?:pattern)`.
     *
     * @param  Regine|string  $pattern  The pattern to include in the non-capturing group.
     */
    public function nonCapturingGroup(Regine|string $pattern): self
    {
        $element = $this->createElementFromPattern($pattern);
        $groupDecorator = new GroupDecorator(
            element: $element,
            groupType: GroupTypesEnum::NON_CAPTURING
        );

        $this->elements->add($groupDecorator);

        return $this;
    }

    /**
     * Adds a named capturing group to the pattern.
     *
     * Creates a group of the form `(?<name>pattern)` using the provided group name and pattern.
     *
     * @param  string  $name  The name of the capturing group.
     * @param  Regine|string  $pattern  The pattern to include within the named group.
     */
    public function namedGroup(string $name, Regine|string $pattern): self
    {
        $element = $this->createElementFromPattern($pattern);
        $groupDecorator = new GroupDecorator(
            element: $element,
            groupType: GroupTypesEnum::NAMED,
            name: $name
        );

        $this->elements->add($groupDecorator);

        return $this;
    }

    /**
     * Adds an atomic (possessive) group to the regex pattern.
     *
     * Wraps the given pattern in an atomic group, preventing backtracking within the group.
     *
     * @param  Regine|string  $pattern  The pattern to include in the atomic group.
     */
    public function atomicGroup(Regine|string $pattern): self
    {
        $element = $this->createElementFromPattern($pattern);
        $groupDecorator = new GroupDecorator(
            element: $element,
            groupType: GroupTypesEnum::ATOMIC
        );

        $this->elements->add($groupDecorator);

        return $this;
    }

    /**
     * Adds a conditional regex group to the pattern.
     *
     * Creates a group of the form `(?(condition)then|else)`, where the pattern used
     * depends on whether the specified condition is met. The else pattern is optional.
     *
     * @param  string  $condition  The condition to evaluate for the group (e.g., a group number or name).
     * @param  Regine|string  $then  The pattern to use if the condition is true.
     * @param  Regine|string|null  $else  The pattern to use if the condition is false (optional).
     */
    public function conditionalGroup(string $condition, Regine|string $then, Regine|string|null $else = null): self
    {
        $thenElement = $this->createElementFromPattern($then);
        $elseElement = $else !== null ? $this->createElementFromPattern($else) : null;

        $groupDecorator = new GroupDecorator(
            element: $thenElement,
            groupType: GroupTypesEnum::CONDITIONAL,
            condition: $condition,
            elseBranch: $elseElement
        );

        $this->elements->add($groupDecorator);

        return $this;
    }

    /**
     * Wrap the last element in the pattern with a capturing group.
     *
     * This is the true decorator approach - it takes the last element
     * and wraps it with a group decorator.
     */
    public function wrapInGroup(): self
    {
        $this->wrapLastElementInGroup(GroupTypesEnum::CAPTURING);

        return $this;
    }

    /**
     * Wrap the last element in the pattern with a non-capturing group.
     */
    public function wrapInNonCapturingGroup(): self
    {
        $this->wrapLastElementInGroup(GroupTypesEnum::NON_CAPTURING);

        return $this;
    }

    /**
     * Wrap the last element in the pattern with a named capturing group.
     */
    public function wrapInNamedGroup(string $name): self
    {
        $this->wrapLastElementInGroup(GroupTypesEnum::NAMED, $name);

        return $this;
    }

    /**
     * Wrap the last element in the pattern with an atomic group.
     */
    public function wrapInAtomicGroup(): self
    {
        $this->wrapLastElementInGroup(GroupTypesEnum::ATOMIC);

        return $this;
    }

    /**
     * Helper method to wrap the last element with a group decorator.
     */
    private function wrapLastElementInGroup(
        GroupTypesEnum $groupType,
        ?string $name = null,
        ?string $condition = null
    ): void {
        $lastElement = $this->elements->getLastElement();

        if ($lastElement === null) {
            throw new LogicException('Cannot wrap in group: no preceding element');
        }

        // Remove the last element and replace it with a grouped version
        $this->elements->removeLast();

        $groupDecorator = new GroupDecorator(
            element: $lastElement,
            groupType: $groupType,
            name: $name,
            condition: $condition
        );

        $this->elements->add($groupDecorator);
    }

    /**
     * Convert a pattern (string or Regine) to a regex element.
     */
    private function createElementFromPattern(
        Regine|string $pattern
    ): RegexElement {
        if ($pattern instanceof Regine) {
            // For Regine objects, use raw pattern component to avoid double-escaping
            return new RawPatternComponent($pattern->compileRaw());
        }

        // For strings in groups, treat them as raw regex patterns
        // Users expect patterns like '\d+' to work as regex, not be escaped as literals
        return new RawPatternComponent($pattern);
    }
}
