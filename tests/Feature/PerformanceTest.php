<?php

declare(strict_types=1);

use Regine\Regine;

it('performs well with complex patterns', function () {
    $iterations = 1000;

    // Benchmark the new component architecture
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $regex = Regine::make()
            ->startOfString()
            ->literal('test')
            ->digit()->exactly(3)
            ->literal('-')
            ->anyOf('abc')
            ->zeroOrMore()
            ->endOfString()
            ->compile();
    }

    $componentTime = microtime(true) - $start;

    // Benchmark simple string concatenation (old approach)
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $regex = '/^test\d{3}\-[abc]*$/';
    }

    $stringTime = microtime(true) - $start;

    echo "\nComponent Architecture: " . ($componentTime * 1000) . " ms\n";
    echo 'String Concatenation: ' . ($stringTime * 1000) . " ms\n";
    echo 'Ratio: ' . ($componentTime / $stringTime) . "x\n";

    // The component architecture should be reasonably fast
    // Allow up to 500x slower than string concatenation (still sub-millisecond for single pattern)
    // Even at 500x slower, we're talking about microseconds per pattern
    expect($componentTime / $stringTime)->toBeLessThan(500);

    // More importantly, single pattern should be very fast
    expect($componentTime / $iterations)->toBeLessThan(0.001); // Less than 1ms per pattern
});

it('handles introspection efficiently', function () {
    $regine = Regine::make()
        ->startOfString()
        ->literal('email')
        ->literal('@')
        ->anyOf('abcdef')
        ->oneOrMore()
        ->literal('.')
        ->letter()
        ->between(2, 4)
        ->endOfString();

    $start = microtime(true);

    for ($i = 0; $i < 1000; $i++) {
        $debug = $regine->debug();
        $description = $regine->describe();
        $metadata = $regine->getMetadata();
    }

    $introspectionTime = microtime(true) - $start;

    echo "\nIntrospection time for 1000 iterations: " . ($introspectionTime * 1000) . " ms\n";

    // Introspection should be fast
    expect($introspectionTime)->toBeLessThan(0.1); // Less than 100ms for 1000 iterations
});

it('provides meaningful introspection data', function () {
    $regine = Regine::make()
        ->literal('hello')
        ->whitespace()
        ->oneOrMore()
        ->anyOf('world')
        ->zeroOrMore();

    $debug = $regine->debug();
    $description = $regine->describe();
    $metadata = $regine->getMetadata();

    expect($debug)->toBeArray();
    expect($debug)->toHaveKeys(['pattern', 'compiled', 'description', 'component_count', 'metadata']);
    expect($debug['component_count'])->toBeGreaterThan(0);
    expect($description)->toBeString();
    expect($metadata)->toBeArray();

    // Test specific pattern
    expect($debug['pattern'])->toBe('hello\s+[world]*');
    expect($debug['compiled'])->toBe('/hello\s+[world]*/');
});

it('maintains fluent interface performance', function () {
    $start = microtime(true);

    for ($i = 0; $i < 1000; $i++) {
        $regex = Regine::make()
            ->literal('start')
            ->digit()
            ->oneOrMore()
            ->literal('middle')
            ->anyOf('abc')
            ->optional()
            ->literal('end')
            ->compile();
    }

    $fluentTime = microtime(true) - $start;

    echo "\nFluent interface time for 1000 iterations: " . ($fluentTime * 1000) . " ms\n";

    // Should be fast enough for practical use
    expect($fluentTime)->toBeLessThan(1.0); // Less than 1 second for 1000 complex patterns
});
