<?php

declare(strict_types=1);

use Regine\Regine;

// Shorthand component tests
it('adds anyChar correctly', function () {
    $regex = Regine::make()->anyChar()->compile();
    expect($regex)->toBe('/./');
});

it('chains basic matchers', function () {
    $regex = Regine::make()->digit()->whitespace()->wordChar()->compile();
    expect($regex)->toBe('/\d\s\w/');
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

// Additional shorthand tests
it('chains all shorthand types', function () {
    $regex = Regine::make()->anyChar()->digit()->nonDigit()->wordChar()->nonWordChar()->whitespace()->nonWhitespace()->compile();
    expect($regex)->toBe('/.\d\D\w\W\s\S/');
});

it('combines shorthand with quantifiers', function () {
    $regex = Regine::make()->digit()->oneOrMore()->whitespace()->zeroOrMore()->wordChar()->exactly(3)->compile();
    expect($regex)->toBe('/\d+\s*\w{3}/');
});

it('combines shorthand with literals', function () {
    $regex = Regine::make()->literal('ID:')->digit()->exactly(5)->compile();
    expect($regex)->toBe('/ID\:\d{5}/');
});

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

it('creates word separation pattern', function () {
    $regex = Regine::make()->wordChar()->oneOrMore()->nonWordChar()->oneOrMore()->wordChar()->oneOrMore()->compile();
    expect($regex)->toBe('/\w+\W+\w+/');
});

it('creates mixed content pattern', function () {
    $regex = Regine::make()->anyChar()->between(1, 100)->compile();
    expect($regex)->toBe('/.{1,100}/');
});

it('creates identifier pattern', function () {
    $regex = Regine::make()->wordChar()->literal('_')->optional()->wordChar()->zeroOrMore()->compile();
    expect($regex)->toBe('/\w_?\w*/');
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
    $regex = Regine::make()->digit()->between(1, 3)->literal('.')->digit()->between(1, 3)->literal('.')->digit()->between(1, 3)->literal('.')->digit()->between(1, 3)->compile();
    expect($regex)->toBe('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/');
});

it('combines all shorthand components in sequence', function () {
    $regex = Regine::make()->anyChar()->digit()->nonDigit()->wordChar()->nonWordChar()->whitespace()->nonWhitespace()->anyChar()->compile();
    expect($regex)->toBe('/.\d\D\w\W\s\S./');
});
