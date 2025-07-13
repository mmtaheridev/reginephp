<?php

declare(strict_types=1);

use Regine\Regine;

// Basic quantifier tests
describe('Basic Quantifiers', function () {
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
});

// Edge case tests
describe('Quantifier Edge Cases', function () {
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
        $regex = Regine::make()->anyChar()->between(0, 5)->compile();
        expect($regex)->toBe('/.{0,5}/');
    });

    it('handles zero quantifier edge cases', function () {
        $regex = Regine::make()->digit()->between(0, 0)->compile();
        expect($regex)->toBe('/\d{0,0}/');
    });

    it('handles one quantifier edge cases', function () {
        $regex = Regine::make()->digit()->between(1, 1)->compile();
        expect($regex)->toBe('/\d{1,1}/');
    });
});

// Error handling tests
describe('Quantifier Error Handling', function () {
    it('throws for negative exactly count', function () {
        Regine::make()->digit()->exactly(-1);
    })->throws(InvalidArgumentException::class, 'Quantifier count must be non-negative.');

    it('throws for negative atLeast count', function () {
        Regine::make()->digit()->atLeast(-1);
    })->throws(InvalidArgumentException::class, 'Quantifier count must be non-negative.');

    it('throws for negative between min', function () {
        Regine::make()->digit()->between(-1, 5);
    })->throws(InvalidArgumentException::class, 'Quantifier counts must be non-negative.');

    it('throws for negative between max', function () {
        Regine::make()->digit()->between(0, -1);
    })->throws(InvalidArgumentException::class, 'Quantifier counts must be non-negative.');

    it('throws for invalid between range', function () {
        Regine::make()->digit()->between(5, 3);
    })->throws(InvalidArgumentException::class, 'Minimum count must be less than or equal to maximum count.');
});

// Integration tests
describe('Quantifier Integration', function () {
    it('chains quantifiers with other methods', function () {
        $regex = Regine::make()->digit()->oneOrMore()->literal('.')->wordChar()->zeroOrMore()->compile();
        expect($regex)->toBe('/\d+\.\w*/');
    });

    it('quantifies literal strings', function () {
        $regex = Regine::make()->literal('abc')->exactly(2)->compile();
        expect($regex)->toBe('/abc{2}/');
    });

    it('quantifies character classes', function () {
        $regex = Regine::make()->anyOf('abc')->between(1, 3)->compile();
        expect($regex)->toBe('/[abc]{1,3}/');
    });

    it('quantifies ranges', function () {
        $regex = Regine::make()->range('a', 'z')->oneOrMore()->compile();
        expect($regex)->toBe('/[a-z]+/');
    });

    it('chains multiple quantifiers on different components', function () {
        $regex = Regine::make()->digit()->exactly(3)->literal('-')->wordChar()->between(2, 5)->compile();
        expect($regex)->toBe('/\d{3}\-\w{2,5}/');
    });

    it('quantifies shorthand components', function () {
        $regex = Regine::make()->whitespace()->oneOrMore()->compile();
        expect($regex)->toBe('/\s+/');
    });

    it('applies quantifiers to letter character class', function () {
        $regex = Regine::make()->letter()->atLeast(3)->compile();
        expect($regex)->toBe('/[a-zA-Z]{3,}/');
    });

    it('quantifies escaped literals', function () {
        $regex = Regine::make()->literal('$')->exactly(2)->compile();
        expect($regex)->toBe('/\${2}/');
    });
});

// Performance and large number tests
describe('Quantifier Performance', function () {
    it('handles large quantifier numbers', function () {
        $regex = Regine::make()->digit()->exactly(1000)->compile();
        expect($regex)->toBe('/\d{1000}/');
    });

    it('handles very large between ranges', function () {
        $regex = Regine::make()->wordChar()->between(100, 1000)->compile();
        expect($regex)->toBe('/\w{100,1000}/');
    });

    it('handles exactly quantifier with large numbers', function () {
        $regex = Regine::make()->anyChar()->exactly(999)->compile();
        expect($regex)->toBe('/.{999}/');
    });

    it('handles atLeast with large numbers', function () {
        $regex = Regine::make()->letter()->atLeast(500)->compile();
        expect($regex)->toBe('/[a-zA-Z]{500,}/');
    });

    it('combines different quantifier types', function () {
        $regex = Regine::make()->digit()->oneOrMore()->literal('-')->wordChar()->exactly(3)->literal('.')->anyChar()->optional()->compile();
        expect($regex)->toBe('/\d+\-\w{3}\..?/');
    });
});
