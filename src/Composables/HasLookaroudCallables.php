<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Regine;

/**
 * This trait is used to extend the Regine class with a lookaround callback syntax.
 *
 * It is used to build a pattern with a callback and then apply a lookaround to it.
 *
 * <code>
 * $regine = Regine::make()
 *     ->lookaheadOf(function (Regine $regine) {
 *         $regine->digit()->nonDigit()->literal('a');
 *     });
 * </code>
 *
 * Composed within Regine and extends the Regine class with a lookaround callback syntax.
 */
trait HasLookaroudCallables
{
    use CompilesCallback;

    /**
     * Create a positive lookahead from a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the lookahead pattern
     */
    public function lookaheadOf(callable $callback): Regine
    {
        $pattern = $this->compileCallback($callback);

        return $this->lookahead($pattern);
    }

    /**
     * Create a negative lookahead from a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the lookahead pattern
     */
    public function negativeLookaheadOf(callable $callback): Regine
    {
        $pattern = $this->compileCallback($callback);

        return $this->negativeLookahead($pattern);
    }

    /**
     * Create a positive lookbehind from a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the lookbehind pattern
     */
    public function lookbehindOf(callable $callback): Regine
    {
        $pattern = $this->compileCallback($callback);

        return $this->lookbehind($pattern);
    }

    /**
     * Create a negative lookbehind from a pattern built with a callback.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the lookbehind pattern
     */
    public function negativeLookbehindOf(callable $callback): Regine
    {
        $pattern = $this->compileCallback($callback);

        return $this->negativeLookbehind($pattern);
    }
}
