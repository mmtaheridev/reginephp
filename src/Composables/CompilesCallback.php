<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Regine;

/**
 * This trait is used to compile a callback into a regex pattern string.
 * 
 * @internal
 */
trait CompilesCallback
{
    /**
     * Compile a callback into a regex pattern string.
     *
     * @param  callable(Regine): Regine  $callback  Function that builds the pattern
     */
    protected function compileCallback(
        callable $callback
    ): string {
        $tempRegine = new self;
        $callback($tempRegine);

        return $tempRegine->compileRaw();
    }
}
