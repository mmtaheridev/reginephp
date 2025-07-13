<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\GroupComponent;
use Regine\Enums\GroupTypesEnum;
use Regine\Regine;

trait HasGroups
{
    /**
     * Add a capturing group
     *
     *  <code>
     *      $regine = Regine::make()->group('abc'); // (abc)
     *      $regine = Regine::make()->group(Regine::make()->literal('test')); // (test)
     *  </code>
     *
     * @param  Regine|string  $pattern  The pattern to group
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
     * Add a non-capturing group
     *
     *  <code>
     *  $regine = Regine::make()->nonCapturingGroup('abc'); // (?:abc)
     *  $regine = Regine::make()->nonCapturingGroup(Regine::make()->literal('test')); // (?:test)
     *  </code>
     *
     * @param  Regine|string  $pattern  The pattern to group
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
     * Add a named capturing group
     *
     *  <code>
     *  $regine = Regine::make()->namedGroup('word', '\w+'); // (?<word>\w+)
     *  $regine = Regine::make()->namedGroup('digits',
     *      Regine::make()->digit()->oneOrMore()
     *  ); // (?<digits>\d+)
     *  </code>
     *
     * @param  string  $name  The name for the group
     * @param  Regine|string  $pattern  The pattern to group
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
     * Add an atomic group (possessive group)
     *
     *  <code>
     *  $regine = Regine::make()->atomicGroup('abc'); // (?>abc)
     *  $regine = Regine::make()->atomicGroup(Regine::make()->literal('test')); // (?>test)
     *  </code>
     *
     * @param  Regine|string  $pattern  The pattern to group
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
     * Add a conditional group
     *
     *  <code>
     *  $regine = Regine::make()->conditionalGroup('1', 'yes', 'no'); // (?(1)yes|no)
     *  $regine = Regine::make()->conditionalGroup('word', 'found'); // (?(word)found)
     *  </code>
     *
     * @param  string  $condition  The condition to check
     * @param  Regine|string  $then  The pattern if condition is true
     * @param  Regine|string|null  $else  The pattern if condition is false (optional)
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
