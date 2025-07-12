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

it('adds anyChar correctly', function () {
    $regex = Regine::make()->anyChar()->compile();
    expect($regex)->toBe('/./');
});

it('chains basic matchers', function () {
    $regex = Regine::make()->digit()->whitespace()->wordChar()->compile();
    expect($regex)->toBe('/\d\s\w/');
});

it('combines with literal', function () {
    $regex = Regine::make()->literal('test')->nonDigit()->compile();
    expect($regex)->toBe('/test\D/');
});

it('adds anyOf correctly', function () {     
    $regex = Regine::make()->anyOf('abc')->compile();     
    expect($regex)->toBe('/[abc]/'); 
});  

it('adds noneOf with escaping', function () {     
    $regex = Regine::make()->noneOf('a-b]^')->compile();     
    expect($regex)->toBe('/[^a\\-b\\]\\^]/');
});

it('adds range', function () {
    $regex = Regine::make()->range('a', 'z')->compile();
    expect($regex)->toBe('/[a-z]/');
});

it('throws for empty anyOf', function () {
    Regine::make()->anyOf('');
})->throws(InvalidArgumentException::class, 'Characters cannot be empty.');

it('throws for empty noneOf', function () {
    Regine::make()->noneOf('');
})->throws(InvalidArgumentException::class, 'Characters cannot be empty.');

it('throws for invalid range characters', function () {
    Regine::make()->range('ab', 'z');
})->throws(InvalidArgumentException::class, 'Range must be single characters.');

it('throws for invalid range order', function () {
    Regine::make()->range('z', 'a');
})->throws(InvalidArgumentException::class, 'Range start must be less than or equal to range end.');

it('escapes special characters in anyOf', function () {
    $regex = Regine::make()->anyOf('a\\b]^-c')->compile();
    expect($regex)->toBe('/[a\\\\b\\]\\^\\-c]/');
});

it('allows equal characters in range', function () {
    $regex = Regine::make()->range('a', 'a')->compile();
    expect($regex)->toBe('/[a-a]/');
});

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

it('applies quantifiers to character classes', function () {
    $regex = Regine::make()->anyOf('abc')->zeroOrMore()->range('0', '9')->exactly(2)->compile();
    expect($regex)->toBe('/[abc]*[0-9]{2}/');
});

// Additional helper method test (letter method used in tests above)
it('adds letter character class', function () {
    $regex = Regine::make()->letter()->compile();
    expect($regex)->toBe('/[a-zA-Z]/');
});

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