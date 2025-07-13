<?php

declare(strict_types=1);

use Regine\Regine;

// Anchor and Boundary Tests
it('adds start of string anchor', function () {
    $regex = Regine::make()->startOfString()->literal('test')->compile();
    expect($regex)->toBe('/^test/');
});

it('adds end of string anchor', function () {
    $regex = Regine::make()->literal('test')->endOfString()->compile();
    expect($regex)->toBe('/test$/');
});

it('adds start of line anchor', function () {
    $regex = Regine::make()->startOfLine()->digit()->oneOrMore()->compile();
    expect($regex)->toBe('/^\d+/');
});

it('adds end of line anchor', function () {
    $regex = Regine::make()->wordChar()->zeroOrMore()->endOfLine()->compile();
    expect($regex)->toBe('/\w*$/');
});

it('adds word boundary', function () {
    $regex = Regine::make()->wordBoundary()->literal('word')->wordBoundary()->compile();
    expect($regex)->toBe('/\bword\b/');
});

it('adds non-word boundary', function () {
    $regex = Regine::make()->nonWordBoundary()->literal('test')->nonWordBoundary()->compile();
    expect($regex)->toBe('/\Btest\B/');
});

it('combines anchors with other patterns', function () {
    $regex = Regine::make()->startOfString()->digit()->exactly(3)->endOfString()->compile();
    expect($regex)->toBe('/^\d{3}$/');
});

it('uses anchors at different positions', function () {
    $regex = Regine::make()->literal('start')->startOfString()->literal('middle')->endOfString()->literal('end')->compile();
    expect($regex)->toBe('/start^middle$end/');
});

it('creates word boundary pattern', function () {
    $regex = Regine::make()->wordBoundary()->letter()->oneOrMore()->wordBoundary()->compile();
    expect($regex)->toBe('/\b[a-zA-Z]+\b/');
});

it('creates complete line pattern', function () {
    $regex = Regine::make()->startOfLine()->anyChar()->oneOrMore()->endOfLine()->compile();
    expect($regex)->toBe('/^.+$/');
});

it('chains anchors with quantifiers', function () {
    $regex = Regine::make()->startOfString()->digit()->atLeast(1)->literal('-')->digit()->exactly(2)->endOfString()->compile();
    expect($regex)->toBe('/^\d{1,}\-\d{2}$/');
});

it('uses multiple word boundaries', function () {
    $regex = Regine::make()->wordBoundary()->literal('hello')->wordBoundary()->whitespace()->oneOrMore()->wordBoundary()->literal('world')->wordBoundary()->compile();
    expect($regex)->toBe('/\bhello\b\s+\bworld\b/');
});

// Additional anchor tests
it('creates exact match with string anchors', function () {
    $regex = Regine::make()->startOfString()->literal('exact')->endOfString()->compile();
    expect($regex)->toBe('/^exact$/');
});

it('creates line-based validation', function () {
    $regex = Regine::make()->startOfLine()->digit()->oneOrMore()->endOfLine()->compile();
    expect($regex)->toBe('/^\d+$/');
});

it('combines all anchor types', function () {
    $regex = Regine::make()->startOfString()->startOfLine()->wordBoundary()->literal('test')->wordBoundary()->endOfLine()->endOfString()->compile();
    expect($regex)->toBe('/^^\btest\b$$/');
});

it('creates word isolation pattern', function () {
    $regex = Regine::make()->wordBoundary()->letter()->between(3, 10)->wordBoundary()->compile();
    expect($regex)->toBe('/\b[a-zA-Z]{3,10}\b/');
});

it('creates non-word boundary pattern', function () {
    $regex = Regine::make()->nonWordBoundary()->anyChar()->oneOrMore()->nonWordBoundary()->compile();
    expect($regex)->toBe('/\B.+\B/');
});

it('alternates word and non-word boundaries', function () {
    $regex = Regine::make()->wordBoundary()->literal('word')->nonWordBoundary()->literal('part')->wordBoundary()->compile();
    expect($regex)->toBe('/\bword\Bpart\b/');
});

it('creates email-like pattern with anchors', function () {
    $regex = Regine::make()->startOfString()->wordChar()->oneOrMore()->literal('@')->wordChar()->oneOrMore()->literal('.')->wordChar()->between(2, 4)->endOfString()->compile();
    expect($regex)->toBe('/^\w+@\w+\.\w{2,4}$/');
});

it('creates phone number pattern with anchors', function () {
    $regex = Regine::make()->startOfString()->digit()->exactly(3)->literal('-')->digit()->exactly(3)->literal('-')->digit()->exactly(4)->endOfString()->compile();
    expect($regex)->toBe('/^\d{3}\-\d{3}\-\d{4}$/');
});

it('creates multi-line start pattern', function () {
    $regex = Regine::make()->startOfLine()->literal('# ')->anyChar()->oneOrMore()->compile();
    expect($regex)->toBe('/^\# .+/');
});

it('creates multi-line end pattern', function () {
    $regex = Regine::make()->anyChar()->oneOrMore()->literal('.')->endOfLine()->compile();
    expect($regex)->toBe('/.+\.$/');
});

it('creates word search pattern', function () {
    $regex = Regine::make()->wordBoundary()->literal('search')->wordBoundary()->compile();
    expect($regex)->toBe('/\bsearch\b/');
});

it('creates partial word match with non-word boundary', function () {
    $regex = Regine::make()->nonWordBoundary()->literal('art')->nonWordBoundary()->compile();
    expect($regex)->toBe('/\Bart\B/');
});

it('validates complete input with string anchors', function () {
    $regex = Regine::make()->startOfString()->range('a', 'z')->oneOrMore()->endOfString()->compile();
    expect($regex)->toBe('/^[a-z]+$/');
});

it('creates strict line validation', function () {
    $regex = Regine::make()->startOfLine()->digit()->between(1, 3)->endOfLine()->compile();
    expect($regex)->toBe('/^\d{1,3}$/');
});
