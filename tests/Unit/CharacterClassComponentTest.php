<?php

declare(strict_types=1);

use Regine\Regine;

// Basic character class tests
describe('Basic Character Classes', function () {
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

    it('allows equal characters in range', function () {
        $regex = Regine::make()->range('a', 'a')->compile();
        expect($regex)->toBe('/[a-a]/');
    });

    it('adds letter character class', function () {
        $regex = Regine::make()->letter()->compile();
        expect($regex)->toBe('/[a-zA-Z]/');
    });

    it('escapes special characters in anyOf', function () {
        $regex = Regine::make()->anyOf('a\\b]^-c')->compile();
        expect($regex)->toBe('/[a\\\\b\\]\\^\\-c]/');
    });

    it('handles special characters in noneOf', function () {
        $regex = Regine::make()->noneOf('^-]\\')->compile();
        expect($regex)->toBe('/[^\\^\\-\\]\\\\]/');
    });

    it('handles single character in anyOf', function () {
        $regex = Regine::make()->anyOf('a')->compile();
        expect($regex)->toBe('/[a]/');
    });

    it('handles single character in noneOf', function () {
        $regex = Regine::make()->noneOf('a')->compile();
        expect($regex)->toBe('/[^a]/');
    });
});

// Error handling tests
describe('Character Class Error Handling', function () {
    it('throws for empty anyOf', function () {
        Regine::make()->anyOf('');
    })->throws(InvalidArgumentException::class, 'Character class cannot be empty.');

    it('throws for empty noneOf', function () {
        Regine::make()->noneOf('');
    })->throws(InvalidArgumentException::class, 'Character class cannot be empty.');

    it('throws for invalid range characters', function () {
        Regine::make()->range('ab', 'z');
    })->throws(InvalidArgumentException::class, 'Range boundaries must be single characters.');

    it('throws for invalid range order', function () {
        Regine::make()->range('z', 'a');
    })->throws(InvalidArgumentException::class, 'Range start must be less than or equal to range end.');
});

// Range tests
describe('Character Ranges', function () {
    it('creates numeric ranges', function () {
        $regex = Regine::make()->range('0', '9')->compile();
        expect($regex)->toBe('/[0-9]/');
    });

    it('creates uppercase letter range', function () {
        $regex = Regine::make()->range('A', 'Z')->compile();
        expect($regex)->toBe('/[A-Z]/');
    });

    it('creates lowercase letter range', function () {
        $regex = Regine::make()->range('a', 'z')->compile();
        expect($regex)->toBe('/[a-z]/');
    });

    it('validates range with special ASCII characters', function () {
        $regex = Regine::make()->range('!', '~')->compile();
        expect($regex)->toBe('/[!-~]/');
    });
});

// Integration tests
describe('Character Class Integration', function () {
    it('applies quantifiers to character classes', function () {
        $regex = Regine::make()->anyOf('abc')->oneOrMore()->compile();
        expect($regex)->toBe('/[abc]+/');
    });

    it('creates complex character class with multiple ranges', function () {
        $regex = Regine::make()->range('a', 'z')->range('A', 'Z')->range('0', '9')->compile();
        expect($regex)->toBe('/[a-z][A-Z][0-9]/');
    });

    it('combines anyOf with other character classes', function () {
        $regex = Regine::make()->anyOf('abc')->digit()->compile();
        expect($regex)->toBe('/[abc]\d/');
    });

    it('chains character classes', function () {
        $regex = Regine::make()->anyOf('abc')->noneOf('xyz')->compile();
        expect($regex)->toBe('/[abc][^xyz]/');
    });
});

// Unicode and special character tests
describe('Unicode and Special Characters', function () {
    it('handles unicode characters in anyOf', function () {
        $regex = Regine::make()->anyOf('αβγ')->compile();
        expect($regex)->toBe('/[αβγ]/');
    });

    it('handles unicode characters in noneOf', function () {
        $regex = Regine::make()->noneOf('αβγ')->compile();
        expect($regex)->toBe('/[^αβγ]/');
    });

    it('handles whitespace in character classes', function () {
        $regex = Regine::make()->anyOf(" \t\n")->compile();
        expect($regex)->toBe("/[ \t\n]/");
    });

    it('handles tabs and newlines in character classes', function () {
        $regex = Regine::make()->anyOf("\t\n\r")->compile();
        expect($regex)->toBe("/[\t\n\r]/");
    });
});
