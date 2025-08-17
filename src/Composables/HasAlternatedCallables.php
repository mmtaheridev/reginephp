<?php

declare(strict_types=1);

namespace Regine\Composables;

use Regine\Regine;

/**
 * This trait is used to extend the Regine class with an alternation callback syntax.
 *
 * It is used to build a pattern with a callback and then apply an alternation to it.
 *
 * <code>
 * $regine = Regine::make()
 *     ->oneOfCallbacks([
 *         fn (Regine $regine) => $regine->digit(),
 *         fn (Regine $regine) => $regine->literal('abc'),
 *         fn (Regine $regine) => $regine->literal('xyz'),
 *     ]);
 * </code>
 *
 * Composed within Regine and extends the Regine class with an alternation callback syntax.
 */
trait HasAlternatedCallables
{
    use CompilesCallback;

    /**
     * Create alternation from an array of callbacks and/or strings.
     *
     * @param  array<callable(Regine): Regine|string>  $alternatives  Array of callbacks or strings
     */
    public function oneOfCallbacks(
        array $alternatives
    ): Regine {
        $compiledAlternatives = [];

        foreach ($alternatives as $alternative) {
            if (is_callable($alternative)) {
                $compiledAlternatives[] = $this->compileCallback($alternative);
            } else {
                $compiledAlternatives[] = (string) $alternative;
            }
        }

        return $this->oneOf($compiledAlternatives);
    }
}
