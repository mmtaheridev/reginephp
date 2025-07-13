<?php

declare(strict_types=1);

use Regine\Regine;

// Quantifier Tests
it('adds zeroOrMore quantifier', function () {
    $regex = Regine::make()->digit()->zeroOrMore()->compile();
    expect($regex)->toBe('/\d*/');
});

it('adds oneOrMore quantifier', function () {
    $regex = Regine::make()->wordChar()->oneOrMore()->compile();
    expect($regex)->toBe('/\w+/');
});

it('adds optional quantifier', function () {
    $regex = Regine::make()->literal('test')->optional()->compile();
    expect($regex)->toBe('/test?/');
});

it('adds exactly quantifier', function () {
    $regex = Regine::make()->digit()->exactly(3)->compile();
    expect($regex)->toBe('/\d{3}/');
});

it('adds atLeast quantifier', function () {
    $regex = Regine::make()->letter()->atLeast(2)->compile();
    expect($regex)->toBe('/[a-zA-Z]{2,}/');
});

it('adds between quantifier', function () {
    $regex = Regine::make()->anyChar()->between(2, 5)->compile();
    expect($regex)->toBe('/.{2,5}/');
});

it('allows exactly zero', function () {
    $regex = Regine::make()->digit()->exactly(0)->compile();
    expect($regex)->toBe('/\d{0}/');
});

it('allows atLeast zero', function () {
    $regex = Regine::make()->wordChar()->atLeast(0)->compile();
    expect($regex)->toBe('/\w{0,}/');
});

it('allows between with same min and max', function () {
    $regex = Regine::make()->anyChar()->between(3, 3)->compile();
    expect($regex)->toBe('/.{3,3}/');
});

it('allows between with min zero', function () {
    $regex = Regine::make()->digit()->between(0, 5)->compile();
    expect($regex)->toBe('/\d{0,5}/');
});

it('throws for negative exactly count', function () {
    Regine::make()->digit()->exactly(-1);
})->throws(InvalidArgumentException::class, 'Quantifier count must be non-negative.');

it('throws for negative atLeast count', function () {
    Regine::make()->wordChar()->atLeast(-1);
})->throws(InvalidArgumentException::class, 'Quantifier count must be non-negative.');

it('throws for negative between min', function () {
    Regine::make()->anyChar()->between(-1, 5);
})->throws(InvalidArgumentException::class, 'Quantifier counts must be non-negative.');

it('throws for negative between max', function () {
    Regine::make()->anyChar()->between(1, -1);
})->throws(InvalidArgumentException::class, 'Quantifier counts must be non-negative.');

it('throws for invalid between range', function () {
    Regine::make()->digit()->between(5, 2);
})->throws(InvalidArgumentException::class, 'Minimum count must be less than or equal to maximum count.');

it('chains quantifiers with other methods', function () {
    $regex = Regine::make()->literal('test')->digit()->oneOrMore()->wordChar()->optional()->compile();
    expect($regex)->toBe('/test\d+\w?/');
});

// Additional quantifier tests
it('quantifies literal strings', function () {
    $regex = Regine::make()->literal('hello')->zeroOrMore()->compile();
    expect($regex)->toBe('/hello*/');
});

it('quantifies character classes', function () {
    $regex = Regine::make()->anyOf('abc')->oneOrMore()->compile();
    expect($regex)->toBe('/[abc]+/');
});

it('quantifies ranges', function () {
    $regex = Regine::make()->range('a', 'z')->exactly(5)->compile();
    expect($regex)->toBe('/[a-z]{5}/');
});

it('chains multiple quantifiers on different components', function () {
    $regex = Regine::make()->digit()->exactly(3)->literal('-')->anyOf('abc')->oneOrMore()->compile();
    expect($regex)->toBe('/\d{3}\-[abc]+/');
});

it('handles large quantifier numbers', function () {
    $regex = Regine::make()->wordChar()->exactly(100)->compile();
    expect($regex)->toBe('/\w{100}/');
});

it('handles very large between ranges', function () {
    $regex = Regine::make()->digit()->between(10, 1000)->compile();
    expect($regex)->toBe('/\d{10,1000}/');
});

it('quantifies shorthand components', function () {
    $regex = Regine::make()->whitespace()->oneOrMore()->nonDigit()->zeroOrMore()->compile();
    expect($regex)->toBe('/\s+\D*/');
});

it('applies quantifiers to letter character class', function () {
    $regex = Regine::make()->letter()->between(1, 10)->compile();
    expect($regex)->toBe('/[a-zA-Z]{1,10}/');
});

it('handles exactly quantifier with large numbers', function () {
    $regex = Regine::make()->anyChar()->exactly(50)->compile();
    expect($regex)->toBe('/.{50}/');
});

it('handles atLeast with large numbers', function () {
    $regex = Regine::make()->wordChar()->atLeast(25)->compile();
    expect($regex)->toBe('/\w{25,}/');
});

it('combines different quantifier types', function () {
    $regex = Regine::make()->digit()->exactly(2)->literal('-')->letter()->between(3, 5)->compile();
    expect($regex)->toBe('/\d{2}\-[a-zA-Z]{3,5}/');
});

it('quantifies escaped literals', function () {
    $regex = Regine::make()->literal('a.b*')->optional()->compile();
    expect($regex)->toBe('/a\.b\*?/');
});

it('handles zero quantifier edge cases', function () {
    $regex = Regine::make()->anyChar()->between(0, 0)->compile();
    expect($regex)->toBe('/.{0,0}/');
});

it('handles one quantifier edge cases', function () {
    $regex = Regine::make()->digit()->between(1, 1)->compile();
    expect($regex)->toBe('/\d{1,1}/');
});
