<?php

declare(strict_types=1);

use Regine\Components\CharacterClassComponent;
use Regine\Enums\CharacterClassTypesEnum;
use Regine\Exceptions\CharacterClass\EmptyCharacterClassException;
use Regine\Exceptions\CharacterClass\RangeBoundariesAreNotSetException;
use Regine\Exceptions\CharacterClass\RangeBoundariesNotSingleCharacterException;
use Regine\Exceptions\CharacterClass\RangeStartGreaterThanEndException;
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

    it('adds noneOfRange', function () {
        $regex = Regine::make()->noneOfRange('a', 'z')->compile();
        expect($regex)->toBe('/[^a-z]/');
    });

    it('allows equal characters in range', function () {
        $regex = Regine::make()->range('a', 'a')->compile();
        expect($regex)->toBe('/[a-a]/');
    });

    it('allows equal characters in noneOfRange', function () {
        $regex = Regine::make()->noneOfRange('a', 'a')->compile();
        expect($regex)->toBe('/[^a-a]/');
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
    })->throws(EmptyCharacterClassException::class);

    it('throws for empty noneOf', function () {
        Regine::make()->noneOf('');
    })->throws(EmptyCharacterClassException::class);

    it('throws for invalid range characters', function () {
        Regine::make()->range('ab', 'z');
    })->throws(RangeBoundariesNotSingleCharacterException::class);

    it('throws for invalid range order', function () {
        Regine::make()->range('z', 'a');
    })->throws(RangeStartGreaterThanEndException::class);

    it('throws for invalid multibyte boundary length', function () {
        // two greek letters -> length 2
        Regine::make()->range('αβ', 'γ');
    })->throws(RangeBoundariesNotSingleCharacterException::class);

    it('throws for invalid emoji range order', function () {
        Regine::make()->range('😇', '😀');
    })->throws(RangeStartGreaterThanEndException::class);

    it('throws for invalid noneOfRange boundary length', function () {
        Regine::make()->noneOfRange('ab', 'z');
    })->throws(RangeBoundariesNotSingleCharacterException::class);

    it('throws for invalid noneOfRange order', function () {
        Regine::make()->noneOfRange('z', 'a');
    })->throws(RangeStartGreaterThanEndException::class);

    it('throws when creating RANGE type without endpoints via constructor', function () {
        expect(fn () => new CharacterClassComponent('a-z', false, CharacterClassTypesEnum::RANGE))
            ->toThrow(RangeBoundariesAreNotSetException::class);
    });

    it('throws when creating RANGE type with only fromChar via constructor', function () {
        expect(fn () => new CharacterClassComponent(
            chars: 'a-z',
            negated: false,
            type: CharacterClassTypesEnum::RANGE,
            fromChar: 'a')
        )->toThrow(RangeBoundariesAreNotSetException::class);
    });
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
        expect($regex)->toBe('/[α-γ]/u');
    });

    it('creates negated unicode noneOfRange with multibyte characters', function () {
        $regex = Regine::make()->noneOfRange('α', 'γ')->compile();
        expect($regex)->toBe('/[^α-γ]/u');
    });

    it('creates emoji range with correct ordering', function () {
        $regex = Regine::make()->range('😀', '😇')->compile();
        expect($regex)->toBe('/[😀-😇]/u');
    });

    it('creates negated emoji noneOfRange with correct ordering', function () {
        $regex = Regine::make()->noneOfRange('😀', '😇')->compile();
        expect($regex)->toBe('/[^😀-😇]/u');
    });

    // FIXED: These tests now expect proper range behavior
    it('handles special-character endpoints in ranges (dash start)', function () {
        $regex = Regine::make()->range('-', 'a')->compile();
        expect($regex)->toBe('/[\\--a]/');
    });

    it('handles special-character endpoints in negated ranges (dash start)', function () {
        $regex = Regine::make()->noneOfRange('-', 'a')->compile();
        expect($regex)->toBe('/[^\\--a]/');
    });

    it('handles special-character endpoints in ranges (dash end)', function () {
        $regex = Regine::make()->range(',', '-')->compile();
        expect($regex)->toBe('/[,-\\-]/');
    });

    it('handles special-character endpoints in negated ranges (dash end)', function () {
        $regex = Regine::make()->noneOfRange(',', '-')->compile();
        expect($regex)->toBe('/[^,-\\-]/');
    });

    it('handles special-character endpoints in ranges (caret start)', function () {
        $regex = Regine::make()->range('^', 'z')->compile();
        expect($regex)->toBe('/[\\^-z]/');
    });

    it('handles special-character endpoints in negated ranges (caret start)', function () {
        $regex = Regine::make()->noneOfRange('^', 'z')->compile();
        expect($regex)->toBe('/[^\\^-z]/');
    });

    it('handles special-character endpoints in ranges (bracket start)', function () {
        $regex = Regine::make()->range('[', 'z')->compile();

        expect($regex)->toBe('/[\\[-z]/');
    });

    it('handles special-character endpoints in ranges (bracket end)', function () {
        $regex = Regine::make()->range('A', ']')->compile();
        expect($regex)->toBe('/[A-\\]]/');
    });

    it('handles range with both endpoints as dashes', function () {
        $regex = Regine::make()->range('-', '-')->compile();
        expect($regex)->toBe('/[\\--\\-]/');
    });

    it('handles range with backslash characters', function () {
        $regex = Regine::make()->range('\\', '\\')->compile();
        expect($regex)->toBe('/[\\\\-\\\\]/');
    });

    // Direct constructor tests
    it('accepts valid range construction via constructor', function () {
        $component = new CharacterClassComponent('a-z', false, CharacterClassTypesEnum::RANGE, 'a', 'z');
        expect($component->compile())->toBe('[a-z]');
    });

    it('accepts valid negated range construction via constructor', function () {
        $component = new CharacterClassComponent('0-9', true, CharacterClassTypesEnum::RANGE, '0', '9');
        expect($component->compile())->toBe('[^0-9]');
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

    it('applies quantifier to noneOfRange', function () {
        $regex = Regine::make()->noneOfRange('0', '9')->oneOrMore()->compile();
        expect($regex)->toBe('/[^0-9]+/');
    });
});

// Metadata tests
describe('Character Range Metadata', function () {
    it('provides correct metadata for basic range', function () {
        $component = CharacterClassComponent::range('a', 'z');
        $metadata = $component->getMetadata();

        expect($metadata['type'])->toBe('character_class')
            ->and($metadata['classType'])->toBe('range')
            ->and($metadata['fromChar'])->toBe('a')
            ->and($metadata['toChar'])->toBe('z')
            ->and($metadata['fromCharCode'])->toBe(mb_ord('a'))
            ->and($metadata['toCharCode'])->toBe(mb_ord('z'))
            ->and($metadata['negated'])->toBe(false);
    });

    it('provides correct metadata for negated range', function () {
        $component = CharacterClassComponent::noneOfRange('0', '9');
        $metadata = $component->getMetadata();

        expect($metadata['negated'])->toBeTrue()
            ->and($metadata['fromChar'])->toBe('0')
            ->and($metadata['toChar'])->toBe('9');
    });

    it('provides correct metadata for Unicode range', function () {
        $component = CharacterClassComponent::range('α', 'ω');
        $metadata = $component->getMetadata();

        expect($metadata['fromChar'])->toBe('α')
            ->and($metadata['toChar'])->toBe('ω')
            ->and($metadata['fromCharCode'])->toBe(mb_ord('α'))
            ->and($metadata['toCharCode'])->toBe(mb_ord('ω'));
    });

    it('provides correct metadata for special character range', function () {
        $component = CharacterClassComponent::range('-', '9');
        $metadata = $component->getMetadata();

        expect($metadata['fromChar'])->toBe('-')
            ->and($metadata['toChar'])->toBe('9')
            ->and($metadata['fromCharCode'])->toBe(mb_ord('-'))
            ->and($metadata['toCharCode'])->toBe(mb_ord('9'));
    });

    it('does not include range metadata for non-range types', function () {
        $component = CharacterClassComponent::anyOf('abc');
        $metadata = $component->getMetadata();

        expect($metadata)->not->toHaveKey('fromChar')
            ->and($metadata)->not->toHaveKey('toChar')
            ->and($metadata)->not->toHaveKey('fromCharCode')
            ->and($metadata)->not->toHaveKey('toCharCode');
    });
});

// Description tests
describe('Character Range Descriptions', function () {
    it('provides correct description for positive range', function () {
        $component = CharacterClassComponent::range('a', 'z');
        expect($component->getDescription())->toBe("Character range: from 'a' to 'z'");
    });

    it('provides correct description for negated range', function () {
        $component = CharacterClassComponent::noneOfRange('0', '9');
        expect($component->getDescription())->toBe("Negated character range: from '0' to '9'");
    });

    it('provides correct description for special character range', function () {
        $component = CharacterClassComponent::range('-', '9');
        expect($component->getDescription())->toBe("Character range: from '-' to '9'");
    });

    it('provides correct description for Unicode range', function () {
        $component = CharacterClassComponent::range('α', 'ω');
        expect($component->getDescription())->toBe("Character range: from 'α' to 'ω'");
    });
});

// Unicode and special character tests
describe('Unicode and Special Characters', function () {
    it('handles unicode characters in anyOf', function () {
        $regex = Regine::make()->anyOf('αβγ')->compile();
        expect($regex)->toBe('/[αβγ]/u');
    });

    it('handles unicode characters in noneOf', function () {
        $regex = Regine::make()->noneOf('αβγ')->compile();
        expect($regex)->toBe('/[^αβγ]/u');
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

// Pattern matching tests to validate the ranges actually work
describe('Range Pattern Matching', function () {
    it('matches characters within basic range', function () {
        $pattern = Regine::make()->range('a', 'z')->compile();
        expect(preg_match($pattern, 'hello'))->toBe(1)
            ->and(preg_match($pattern, 'A'))->toBe(0)
            ->and(preg_match($pattern, '5'))->toBe(0);
    });

    it('excludes characters outside negated range', function () {
        $pattern = Regine::make()->noneOfRange('0', '9')->compile();
        expect(preg_match($pattern, 'a'))->toBe(1)
            ->and(preg_match($pattern, 'Z'))->toBe(1)
            ->and(preg_match($pattern, '5'))->toBe(0);
    });

    it('handles special character range correctly', function () {
        $pattern = Regine::make()->range('-', '9')->compile();
        expect(preg_match($pattern, '-'))->toBe(1)
            ->and(preg_match($pattern, '0'))->toBe(1)
            ->and(preg_match($pattern, '9'))->toBe(1)
            ->and(preg_match($pattern, 'a'))->toBe(0)
            ->and(preg_match($pattern, 'A'))->toBe(0);
    });

    it('handles unicode range correctly', function () {
        $pattern = Regine::make()->range('α', 'γ')->compile();
        expect(preg_match($pattern, 'α'))->toBe(1)
            ->and(preg_match($pattern, 'β'))->toBe(1)
            ->and(preg_match($pattern, 'γ'))->toBe(1)
            ->and(preg_match($pattern, 'δ'))->toBe(0)
            ->and(preg_match($pattern, 'a'))->toBe(0);
    });
});
