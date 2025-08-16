<?php

declare(strict_types=1);

use Regine\Regine;

// Basic shorthand tests
describe('Basic Shorthand Components', function () {
    it('adds anyChar correctly', function () {
        $regex = Regine::make()->anyChar()->compile();
        expect($regex)->toBe('/./');
    });

    it('adds digit shorthand', function () {
        $regex = Regine::make()->digit()->compile();
        expect($regex)->toBe('/\d/');
    });

    it('adds non-digit shorthand', function () {
        $regex = Regine::make()->nonDigit()->compile();
        expect($regex)->toBe('/\D/');
    });

    it('adds word character shorthand', function () {
        $regex = Regine::make()->wordChar()->compile();
        expect($regex)->toBe('/\w/');
    });

    it('adds non-word character shorthand', function () {
        $regex = Regine::make()->nonWordChar()->compile();
        expect($regex)->toBe('/\W/');
    });

    it('adds whitespace shorthand', function () {
        $regex = Regine::make()->whitespace()->compile();
        expect($regex)->toBe('/\s/');
    });

    it('adds non-whitespace shorthand', function () {
        $regex = Regine::make()->nonWhitespace()->compile();
        expect($regex)->toBe('/\S/');
    });

    it('chains basic matchers', function () {
        $regex = Regine::make()->digit()->whitespace()->wordChar()->compile();
        expect($regex)->toBe('/\d\s\w/');
    });
});

// Shorthand chaining tests
describe('Shorthand Chaining', function () {
    it('chains all shorthand types', function () {
        $regex = Regine::make()->anyChar()->digit()->nonDigit()->wordChar()->nonWordChar()->whitespace()->nonWhitespace()->compile();
        expect($regex)->toBe('/.\d\D\w\W\s\S/');
    });

    it('combines shorthand with quantifiers', function () {
        $regex = Regine::make()->digit()->oneOrMore()->whitespace()->zeroOrMore()->compile();
        expect($regex)->toBe('/\d+\s*/');
    });

    it('combines shorthand with literals', function () {
        $regex = Regine::make()->wordChar()->oneOrMore()->literal('@')->wordChar()->oneOrMore()->compile();
        expect($regex)->toBe('/\w+@\w+/');
    });
});

// Pattern building tests
describe('Pattern Building with Shorthands', function () {
    it('creates phone number pattern with shortcuts', function () {
        $regex = Regine::make()->digit()->exactly(3)->literal('-')->digit()->exactly(3)->literal('-')->digit()->exactly(4)->compile();
        expect($regex)->toBe('/\d{3}\-\d{3}\-\d{4}/');
    });

    it('creates email pattern with shortcuts', function () {
        $regex = Regine::make()->wordChar()->oneOrMore()->literal('@')->wordChar()->oneOrMore()->literal('.')->wordChar()->between(2, 4)->compile();
        expect($regex)->toBe('/\w+@\w+\.\w{2,4}/');
    });

    it('creates whitespace handling pattern', function () {
        $regex = Regine::make()->nonWhitespace()->oneOrMore()->whitespace()->oneOrMore()->nonWhitespace()->oneOrMore()->compile();
        expect($regex)->toBe('/\S+\s+\S+/');
    });

    it('creates alphanumeric pattern', function () {
        $regex = Regine::make()->wordChar()->oneOrMore()->compile();
        expect($regex)->toBe('/\w+/');
    });

    it('creates non-alphanumeric pattern', function () {
        $regex = Regine::make()->nonWordChar()->oneOrMore()->compile();
        expect($regex)->toBe('/\W+/');
    });

    it('creates numeric validation pattern', function () {
        $regex = Regine::make()->digit()->oneOrMore()->compile();
        expect($regex)->toBe('/\d+/');
    });

    it('creates non-numeric pattern', function () {
        $regex = Regine::make()->nonDigit()->oneOrMore()->compile();
        expect($regex)->toBe('/\D+/');
    });

    it('creates any character pattern', function () {
        $regex = Regine::make()->anyChar()->oneOrMore()->compile();
        expect($regex)->toBe('/.+/');
    });
});

// Complex pattern tests
describe('Complex Shorthand Patterns', function () {
    it('creates word separation pattern', function () {
        $regex = Regine::make()->wordChar()->oneOrMore()->whitespace()->oneOrMore()->wordChar()->oneOrMore()->compile();
        expect($regex)->toBe('/\w+\s+\w+/');
    });

    it('creates mixed content pattern', function () {
        $regex = Regine::make()->digit()->oneOrMore()->nonDigit()->wordChar()->oneOrMore()->compile();
        expect($regex)->toBe('/\d+\D\w+/');
    });

    it('creates identifier pattern', function () {
        $regex = Regine::make()->wordChar()->oneOrMore()->compile();
        expect($regex)->toBe('/\w+/');
    });

    it('creates decimal number pattern', function () {
        $regex = Regine::make()->digit()->oneOrMore()->literal('.')->digit()->oneOrMore()->compile();
        expect($regex)->toBe('/\d+\.\d+/');
    });

    it('creates optional decimal pattern', function () {
        $regex = Regine::make()->digit()->oneOrMore()->literal('.')->optional()->digit()->zeroOrMore()->compile();
        expect($regex)->toBe('/\d+\.?\d*/');
    });

    it('creates whitespace normalization pattern', function () {
        $regex = Regine::make()->whitespace()->oneOrMore()->compile();
        expect($regex)->toBe('/\s+/');
    });

    it('creates word extraction pattern', function () {
        $regex = Regine::make()->wordChar()->oneOrMore()->compile();
        expect($regex)->toBe('/\w+/');
    });

    it('creates punctuation pattern', function () {
        $regex = Regine::make()->nonWordChar()->nonWhitespace()->compile();
        expect($regex)->toBe('/\W\S/');
    });

    it('creates complex validation pattern', function () {
        $regex = Regine::make()->digit()->oneOrMore()->literal('-')->wordChar()->exactly(3)->literal('.')->anyChar()->optional()->compile();
        expect($regex)->toBe('/\d+\-\w{3}\..?/');
    });

    it('combines all shorthand components in sequence', function () {
        $regex = Regine::make()->anyChar()->digit()->nonDigit()->wordChar()->nonWordChar()->whitespace()->nonWhitespace()->compile();
        expect($regex)->toBe('/.\d\D\w\W\s\S/');
    });
});
