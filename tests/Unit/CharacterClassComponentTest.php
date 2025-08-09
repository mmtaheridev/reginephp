<?php

declare(strict_types=1);

use Regine\Components\CharacterClassComponent;
use Regine\Enums\CharacterClassTypesEnum;
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

    it('throws for invalid multibyte boundary length', function () {
        // two greek letters -> length 2
        Regine::make()->range('αβ', 'γ');
    })->throws(InvalidArgumentException::class, 'Range boundaries must be single characters.');

    it('throws for invalid emoji range order', function () {
        Regine::make()->range('😇', '😀');
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

    it('creates unicode range with multibyte characters', function () {
        $regex = Regine::make()->range('α', 'γ')->compile();
        expect($regex)->toBe('/[α-γ]/');
    });

    it('creates emoji range with correct ordering', function () {
        $regex = Regine::make()->range('😀', '😇')->compile();
        expect($regex)->toBe('/[😀-😇]/');
    });

    it('parses range when internal string has variable length parts', function () {
        // Directly construct to simulate future format changes ("ab-cd")
        $component = new CharacterClassComponent('ab-cd', false, CharacterClassTypesEnum::RANGE);
        expect($component->compile())->toBe('[a-d]');
        expect($component->getDescription())->toBe("Character range: from 'a' to 'd'");
    });

    it('parses multibyte range when internal string has variable length parts', function () {
        $component = new CharacterClassComponent('αβ-γδ', false, CharacterClassTypesEnum::RANGE);
        expect($component->compile())->toBe('[α-δ]');
        expect($component->getDescription())->toBe("Character range: from 'α' to 'δ'");
    });

    it('falls back to escaping when range format is unexpected (no dash)', function () {
        $component = new CharacterClassComponent('abc', false, CharacterClassTypesEnum::RANGE);
        expect($component->compile())->toBe('[abc]');
    });

    it('falls back to escaping when range format has missing endpoints', function () {
        $component1 = new CharacterClassComponent('a-', false, CharacterClassTypesEnum::RANGE);
        $component2 = new CharacterClassComponent('-a', false, CharacterClassTypesEnum::RANGE);
        expect($component1->compile())->toBe('[a\-]');
        expect($component2->compile())->toBe('[\-a]');
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
