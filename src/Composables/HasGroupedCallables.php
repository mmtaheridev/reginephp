<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\LiteralComponent;
use Regine\Decorators\GroupDecorator;
use Regine\Enums\GroupTypesEnum;
use Regine\Regine;

/**
 * This trait is used to extend the Regine class with a grouped callback syntax.
 *
 * It is used to build a pattern with a callback and then apply a group to it.
 *
 * <code>
 * $regine = Regine::make()
 *     ->groupOf(function (Regine $regine) {
 *         $regine->oneOf(['adidas', 'nike'])->literal('Company');
 *     });
 * </code>
 *
 * Composed within Regine and extends the Regine class with a grouped callback syntax.
 */
trait HasGroupedCallables
{
    use CompilesCallback;

    /**
     * Create a capturing group from a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the group content
     */
    public function groupOf(callable $callback): Regine
    {
        return $this->applyGroupToCallback(GroupTypesEnum::CAPTURING, $callback);
    }

    /**
     * Create a non-capturing group from a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the group content
     */
    public function nonCapturingGroupOf(callable $callback): Regine
    {
        return $this->applyGroupToCallback(GroupTypesEnum::NON_CAPTURING, $callback);
    }

    /**
     * Create a named capturing group from a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the group content
     */
    public function namedGroupOf(string $name, callable $callback): Regine
    {
        return $this->applyGroupToCallback(GroupTypesEnum::NAMED, $callback, $name);
    }

    /**
     * Create an atomic group from a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the group content
     */
    public function atomicGroupOf(callable $callback): Regine
    {
        return $this->applyGroupToCallback(GroupTypesEnum::ATOMIC, $callback);
    }

    /**
     * Create a conditional group from patterns built with callbacks.
     *
     * @param  callable(Regine): Regine  $thenCallback  Function that builds the 'then' pattern
     * @param  callable(Regine): Regine|null  $elseCallback  Function that builds the 'else' pattern
     */
    public function conditionalGroupOf(
        string $condition,
        callable $thenCallback,
        ?callable $elseCallback = null
    ): Regine {
        $thenPattern = $this->compileCallback($thenCallback);
        $elsePattern = $elseCallback ? $this->compileCallback($elseCallback) : null;

        return $this->conditionalGroup($condition, $thenPattern, $elsePattern);
    }

    /**
     * Apply a group decorator to a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the group content
     * @param  string|null  $name  Name of the group if it is a named group if not null
     */
    protected function applyGroupToCallback(
        GroupTypesEnum $groupType,
        callable $callback,
        ?string $name = null
    ): Regine {
        $pattern = $this->compileCallback($callback);
        $element = new LiteralComponent($pattern);

        $groupDecorator = new GroupDecorator(
            element: $element,
            groupType: $groupType,
            name: $name
        );

        $this->elements->add($groupDecorator);

        return $this;
    }
}
