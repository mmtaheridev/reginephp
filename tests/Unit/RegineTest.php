<?php

// tests/Unit/RegineTest.php
use Regine\Regine;

it('creates a new instance with make', function () {
    expect(Regine::make())->toBeInstanceOf(Regine::class);
});

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
