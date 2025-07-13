<?php

declare(strict_types=1);

use Regine\Regine;

// Basic literal text tests
it('adds literal text and compiles correctly', function () {
    $regex = Regine::make()->literal('abc')->compile();
    expect($regex)->toBe('/abc/');
});

it('escapes special characters in literal', function () {
    $regex = Regine::make()->literal('a.b*')->compile();
    expect($regex)->toBe('/a\.b\*/');
});

it('throws exception for empty literal', function () {
    Regine::make()->literal('');
})->throws(InvalidArgumentException::class, 'Literal text cannot be empty.');

it('combines literal with other components', function () {
    $regex = Regine::make()->literal('test')->nonDigit()->compile();
    expect($regex)->toBe('/test\D/');
});

// Additional literal tests
it('escapes multiple special characters in literal', function () {
    $regex = Regine::make()->literal('a.b*c+d?e^f$g|h(i)j[k]l{m}n\\o')->compile();
    expect($regex)->toBe('/a\.b\*c\+d\?e\^f\$g\|h\(i\)j\[k\]l\{m\}n\\\\o/');
});

it('handles literal with spaces', function () {
    $regex = Regine::make()->literal('hello world')->compile();
    expect($regex)->toBe('/hello world/');
});

it('handles literal with tabs and newlines', function () {
    $regex = Regine::make()->literal("test\twith\nnewline")->compile();
    expect($regex)->toBe("/test\twith\nnewline/");
});

it('handles literal with unicode characters', function () {
    $regex = Regine::make()->literal('café €')->compile();
    expect($regex)->toBe('/café €/');
});

it('handles empty-looking but valid literal with spaces', function () {
    $regex = Regine::make()->literal('   ')->compile();
    expect($regex)->toBe('/   /');
});

it('chains multiple literals', function () {
    $regex = Regine::make()->literal('start')->literal('middle')->literal('end')->compile();
    expect($regex)->toBe('/startmiddleend/');
});

it('handles literal with regex-like content', function () {
    $regex = Regine::make()->literal('/[a-z]+/')->compile();
    expect($regex)->toBe('/\/\[a\-z\]\+\//');
});
