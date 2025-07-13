<?php

declare(strict_types=1);

use Regine\Regine;

// Basic Regine functionality tests
it('creates a new instance with make', function () {
    expect(Regine::make())->toBeInstanceOf(Regine::class);
});

it('compiles to basic regex format', function () {
    $regex = Regine::make()->compile();
    expect($regex)->toBe('//');
});

it('handles empty pattern compilation', function () {
    $regine = Regine::make();
    expect($regine->compile())->toBe('//');
});

// Integration tests that combine multiple components
it('creates complex email validation pattern', function () {
    $regex = Regine::make()
        ->startOfString()
        ->wordChar()->oneOrMore()
        ->anyOf('.-_')->optional()
        ->wordChar()->zeroOrMore()
        ->literal('@')
        ->wordChar()->oneOrMore()
        ->literal('.')
        ->wordChar()->between(2, 4)
        ->endOfString()
        ->compile();

    expect($regex)->toBe('/^\w+[.\-_]?\w*@\w+\.\w{2,4}$/');
});

it('creates complex phone number validation pattern', function () {
    $regex = Regine::make()
        ->startOfString()
        ->literal('(')->optional()
        ->digit()->exactly(3)
        ->literal(')')->optional()
        ->anyOf(' -')->optional()
        ->digit()->exactly(3)
        ->anyOf(' -')
        ->digit()->exactly(4)
        ->endOfString()
        ->compile();

    expect($regex)->toBe('/^\(?\d{3}\)?[ \-]?\d{3}[ \-]\d{4}$/');
});

it('creates complex password validation pattern', function () {
    $regex = Regine::make()
        ->startOfString()
        ->anyChar()->atLeast(8)
        ->endOfString()
        ->compile();

    expect($regex)->toBe('/^.{8,}$/');
});

it('creates URL validation pattern', function () {
    $regex = Regine::make()
        ->startOfString()
        ->literal('http')
        ->literal('s')->optional()
        ->literal('://')
        ->wordChar()->oneOrMore()
        ->literal('.')
        ->wordChar()->between(2, 4)
        ->anyChar()->zeroOrMore()
        ->endOfString()
        ->compile();

    expect($regex)->toBe('/^https?\:\/\/\w+\.\w{2,4}.*$/');
});

it('creates date validation pattern', function () {
    $regex = Regine::make()
        ->startOfString()
        ->digit()->between(1, 2)
        ->literal('/')
        ->digit()->between(1, 2)
        ->literal('/')
        ->digit()->exactly(4)
        ->endOfString()
        ->compile();

    expect($regex)->toBe('/^\d{1,2}\/\d{1,2}\/\d{4}$/');
});

it('creates IP address validation pattern', function () {
    $regex = Regine::make()
        ->startOfString()
        ->digit()->between(1, 3)
        ->literal('.')
        ->digit()->between(1, 3)
        ->literal('.')
        ->digit()->between(1, 3)
        ->literal('.')
        ->digit()->between(1, 3)
        ->endOfString()
        ->compile();

    expect($regex)->toBe('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/');
});

it('creates username validation pattern', function () {
    $regex = Regine::make()
        ->startOfString()
        ->wordBoundary()
        ->wordChar()->between(3, 16)
        ->wordBoundary()
        ->endOfString()
        ->compile();

    expect($regex)->toBe('/^\b\w{3,16}\b$/');
});

it('creates markdown header pattern', function () {
    $regex = Regine::make()
        ->startOfLine()
        ->literal('#')->oneOrMore()
        ->whitespace()
        ->anyChar()->oneOrMore()
        ->endOfLine()
        ->compile();

    expect($regex)->toBe('/^\#+\s.+$/');
});

it('creates file extension pattern', function () {
    $regex = Regine::make()
        ->anyChar()->oneOrMore()
        ->literal('.')
        ->wordChar()->between(2, 4)
        ->endOfString()
        ->compile();

    expect($regex)->toBe('/.+\.\w{2,4}$/');
});

it('creates HTML tag pattern', function () {
    $regex = Regine::make()
        ->literal('<')
        ->wordChar()->oneOrMore()
        ->anyChar()->zeroOrMore()
        ->literal('>')
        ->compile();

    expect($regex)->toBe('/\<\w+.*\>/');
});

// Method chaining tests
it('supports fluent interface chaining', function () {
    $regine = Regine::make()
        ->literal('test')
        ->digit()
        ->zeroOrMore()
        ->wordBoundary();

    expect($regine)->toBeInstanceOf(Regine::class);
    expect($regine->compile())->toBe('/test\d*\b/');
});

it('handles long method chains', function () {
    $regex = Regine::make()
        ->startOfString()
        ->literal('prefix')
        ->digit()->exactly(3)
        ->literal('-')
        ->wordChar()->between(2, 5)
        ->literal('_')
        ->anyChar()->oneOrMore()
        ->endOfString()
        ->compile();

    expect($regex)->toBe('/^prefix\d{3}\-\w{2,5}_.+$/');
});

// Edge case tests
it('handles multiple consecutive quantifiers', function () {
    $regex = Regine::make()
        ->digit()->oneOrMore()
        ->whitespace()->zeroOrMore()
        ->wordChar()->optional()
        ->compile();

    expect($regex)->toBe('/\d+\s*\w?/');
});

it('handles mixed anchors and boundaries', function () {
    $regex = Regine::make()
        ->startOfString()
        ->wordBoundary()
        ->literal('word')
        ->wordBoundary()
        ->endOfString()
        ->compile();

    expect($regex)->toBe('/^\bword\b$/');
});

it('handles pattern with all component types', function () {
    $regex = Regine::make()
        ->startOfString()                    // Anchor
        ->literal('prefix')                  // Literal
        ->digit()->exactly(3)               // Shorthand + Quantifier
        ->anyOf('abc')->optional()          // Character class + Quantifier
        ->wordBoundary()                    // Anchor
        ->endOfString()                     // Anchor
        ->compile();

    expect($regex)->toBe('/^prefix\d{3}[abc]?\b$/');
});
