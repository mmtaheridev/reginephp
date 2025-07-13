<?php

declare(strict_types=1);

use Regine\Regine;

// Basic anchor tests
describe('Basic Anchors', function () {
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

    it('combines anchors with other patterns', function () {
        $regex = Regine::make()->startOfString()->digit()->exactly(3)->endOfString()->compile();
        expect($regex)->toBe('/^\d{3}$/');
    });

    it('uses anchors at different positions', function () {
        $regex = Regine::make()->literal('start')->startOfString()->literal('middle')->endOfString()->literal('end')->compile();
        expect($regex)->toBe('/start^middle$end/');
    });
});

// Word boundary tests
describe('Word Boundaries', function () {
    it('adds word boundary', function () {
        $regex = Regine::make()->wordBoundary()->literal('word')->wordBoundary()->compile();
        expect($regex)->toBe('/\bword\b/');
    });

    it('adds non-word boundary', function () {
        $regex = Regine::make()->nonWordBoundary()->literal('test')->nonWordBoundary()->compile();
        expect($regex)->toBe('/\Btest\B/');
    });

    it('creates word boundary pattern', function () {
        $regex = Regine::make()->wordBoundary()->letter()->oneOrMore()->wordBoundary()->compile();
        expect($regex)->toBe('/\b[a-zA-Z]+\b/');
    });

    it('creates complete line pattern', function () {
        $regex = Regine::make()->startOfLine()->wordChar()->oneOrMore()->wordBoundary()->endOfLine()->compile();
        expect($regex)->toBe('/^\w+\b$/');
    });

    it('uses multiple word boundaries', function () {
        $regex = Regine::make()->wordBoundary()->literal('hello')->wordBoundary()->literal(' ')->wordBoundary()->literal('world')->wordBoundary()->compile();
        expect($regex)->toBe('/\bhello\b \bworld\b/');
    });

    it('creates non-word boundary pattern', function () {
        $regex = Regine::make()->nonWordBoundary()->literal('test')->nonWordBoundary()->compile();
        expect($regex)->toBe('/\Btest\B/');
    });

    it('alternates word and non-word boundaries', function () {
        $regex = Regine::make()->wordBoundary()->literal('start')->nonWordBoundary()->literal('middle')->wordBoundary()->literal('end')->compile();
        expect($regex)->toBe('/\bstart\Bmiddle\bend/');
    });
});

// Complex pattern tests
describe('Complex Anchor Patterns', function () {
    it('chains anchors with quantifiers', function () {
        $regex = Regine::make()->startOfString()->digit()->exactly(3)->wordBoundary()->letter()->oneOrMore()->endOfString()->compile();
        expect($regex)->toBe('/^\d{3}\b[a-zA-Z]+$/');
    });

    it('creates exact match with string anchors', function () {
        $regex = Regine::make()->startOfString()->literal('exact')->endOfString()->compile();
        expect($regex)->toBe('/^exact$/');
    });

    it('creates line-based validation', function () {
        $regex = Regine::make()->startOfLine()->digit()->oneOrMore()->literal(':')->literal(' ')->wordChar()->oneOrMore()->endOfLine()->compile();
        expect($regex)->toBe('/^\d+\: \w+$/');
    });

    it('combines all anchor types', function () {
        $regex = Regine::make()->startOfString()->wordBoundary()->literal('word')->wordBoundary()->literal(' ')->nonWordBoundary()->literal('test')->nonWordBoundary()->endOfString()->compile();
        expect($regex)->toBe('/^\bword\b \Btest\B$/');
    });

    it('creates word isolation pattern', function () {
        $regex = Regine::make()->wordBoundary()->literal('isolated')->wordBoundary()->compile();
        expect($regex)->toBe('/\bisolated\b/');
    });

    it('creates word search pattern', function () {
        $regex = Regine::make()->wordBoundary()->literal('search')->wordBoundary()->compile();
        expect($regex)->toBe('/\bsearch\b/');
    });

    it('creates partial word match with non-word boundary', function () {
        $regex = Regine::make()->nonWordBoundary()->literal('part')->nonWordBoundary()->compile();
        expect($regex)->toBe('/\Bpart\B/');
    });

    it('validates complete input with string anchors', function () {
        $regex = Regine::make()->startOfString()->literal('complete')->endOfString()->compile();
        expect($regex)->toBe('/^complete$/');
    });

    it('creates strict line validation', function () {
        $regex = Regine::make()->startOfLine()->literal('strict')->endOfLine()->compile();
        expect($regex)->toBe('/^strict$/');
    });
});

// Real-world pattern tests
describe('Real-world Anchor Patterns', function () {
    it('creates email-like pattern with anchors', function () {
        $regex = Regine::make()->startOfString()->wordChar()->oneOrMore()->literal('@')->wordChar()->oneOrMore()->literal('.')->wordChar()->between(2, 4)->endOfString()->compile();
        expect($regex)->toBe('/^\w+@\w+\.\w{2,4}$/');
    });

    it('creates phone number pattern with anchors', function () {
        $regex = Regine::make()->startOfString()->digit()->exactly(3)->literal('-')->digit()->exactly(3)->literal('-')->digit()->exactly(4)->endOfString()->compile();
        expect($regex)->toBe('/^\d{3}\-\d{3}\-\d{4}$/');
    });

    it('creates multi-line start pattern', function () {
        $regex = Regine::make()->startOfLine()->literal('Line')->literal(' ')->digit()->oneOrMore()->compile();
        expect($regex)->toBe('/^Line \d+/');
    });

    it('creates multi-line end pattern', function () {
        $regex = Regine::make()->literal('End')->literal(' ')->digit()->oneOrMore()->endOfLine()->compile();
        expect($regex)->toBe('/End \d+$/');
    });
});
