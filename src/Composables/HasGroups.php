<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\GroupComponent;
use Regine\Enums\GroupTypesEnum;
use Regine\Regine;

trait HasGroups
{
    /**
     * Adds a capturing group to the pattern.
     *
     * Wraps the given pattern in a capturing group, equivalent to `(pattern)` in regular expressions. Accepts either a string pattern or a Regine instance.
     *
     * @param  Regine|string  $pattern  The pattern to include in the capturing group.
     */
    public function group(Regine|string $pattern): self
    {
        $component = new GroupComponent(
            GroupTypesEnum::CAPTURING,
            $pattern
        );

        $this->components->add($component);

        return $this;
    }

    /**
     * Adds a non-capturing group to the regex pattern.
     *
     * Wraps the given pattern in a non-capturing group `(?:pattern)` and adds it to the pattern builder.
     *
     * @param  Regine|string  $pattern  The pattern to include in the non-capturing group.
     */
    public function nonCapturingGroup(Regine|string $pattern): self
    {
        $component = new GroupComponent(
            GroupTypesEnum::NON_CAPTURING,
            $pattern
        );

        $this->components->add($component);

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
        $component = new GroupComponent(
            GroupTypesEnum::NAMED,
            $pattern,
            $name
        );

        $this->components->add($component);

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
        $component = new GroupComponent(
            GroupTypesEnum::ATOMIC,
            $pattern
        );

        $this->components->add($component);

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
        $component = new GroupComponent(
            GroupTypesEnum::CONDITIONAL,
            $then,
            null,
            $condition,
            $else
        );

        $this->components->add($component);

        return $this;
    }
}
