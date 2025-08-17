<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Components\LiteralComponent;
use Regine\Decorators\QuantifierDecorator;
use Regine\Enums\QuantifierTypesEnum;
use Regine\Regine;

/**
 * This trait is used to extend the Regine class with a quantifier callback syntax.
 *
 * It is used to build a pattern with a callback and then apply a quantifier to it.
 *
 * <code>
 * $regine = Regine::make()
 *     ->zeroOrMoreOf(function (Regine $regine) {
 *         $regine->literal('a');
 *     });
 * </code>
 *
 * Composed within Regine and extends the Regine class with a quantifier callback syntax.
 */
trait HasQunatifiedCallables
{
    use CompilesCallback;

    /**
     * Apply a quantifier to a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the pattern to be quantified
     */
    public function zeroOrMoreOf(callable $callback): Regine
    {
        return $this->applyQuantifierToCallback(QuantifierTypesEnum::ZERO_OR_MORE, $callback);
    }

    /**
     * Apply a quantifier to a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the pattern to be quantified
     */
    public function oneOrMoreOf(callable $callback): Regine
    {
        return $this->applyQuantifierToCallback(QuantifierTypesEnum::ONE_OR_MORE, $callback);
    }

    /**
     * Apply a quantifier to a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the pattern to be quantified
     */
    public function optionalOf(callable $callback): Regine
    {
        return $this->applyQuantifierToCallback(QuantifierTypesEnum::OPTIONAL, $callback);
    }

    /**
     * Apply a quantifier to a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the pattern to be quantified
     */
    public function exactlyOf(int $count, callable $callback): self
    {
        return $this->applyQuantifierToCallback(QuantifierTypesEnum::EXACTLY, $callback, ['count' => $count]);
    }

    /**
     * Apply a quantifier to a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the pattern to be quantified
     */
    public function atLeastOf(int $min, callable $callback): self
    {
        return $this->applyQuantifierToCallback(QuantifierTypesEnum::AT_LEAST, $callback, ['min' => $min]);
    }

    /**
     * Apply a quantifier to a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the pattern to be quantified
     */
    public function betweenOf(int $min, int $max, callable $callback): self
    {
        return $this->applyQuantifierToCallback(QuantifierTypesEnum::BETWEEN, $callback, ['min' => $min, 'max' => $max]);
    }

    /**
     * Apply a quantifier decorator to a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the pattern to be quantified
     * @param  array<string, int>  $parameters
     */
    protected function applyQuantifierToCallback(
        QuantifierTypesEnum $quantifierType,
        callable $callback,
        array $parameters = []
    ): Regine {
        $pattern = $this->compileCallback($callback);
        $element = new LiteralComponent($pattern);

        $quantifierDecorator = new QuantifierDecorator(
            element: $element,
            quantifierType: $quantifierType,
            parameters: $parameters
        );

        $this->elements->add($quantifierDecorator);

        return $this;
    }
}
