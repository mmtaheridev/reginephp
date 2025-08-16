<?php

declare(strict_types=1);

use Regine\Enums\RegexFlagsEnum;
use Regine\Regine;

describe('Individual Flag Methods', function () {
    it('adds case insensitive flag', function () {
        $regex = Regine::make()->literal('test')->caseInsensitive()->compile();
        expect($regex)->toBe('/test/i');
    });

    it('adds multiline flag', function () {
        $regex = Regine::make()->startOfString()->literal('test')->multiline()->compile();
        expect($regex)->toBe('/^test/m');
    });

    it('adds dot all (single line) flag', function () {
        $regex = Regine::make()->anyChar()->oneOrMore()->dotAll()->compile();
        expect($regex)->toBe('/.+/s');
    });

    it('adds extended flag', function () {
        $regex = Regine::make()->literal('test')->extended()->compile();
        expect($regex)->toBe('/test/x');
    });

    it('adds unicode flag', function () {
        $regex = Regine::make()->literal('тест')->unicode()->compile();
        expect($regex)->toBe('/тест/u');
    });
});

describe('Alias Methods', function () {
    it('adds case insensitive flag using i()', function () {
        $regex = Regine::make()->literal('test')->i()->compile();
        expect($regex)->toBe('/test/i');
    });

    it('adds multiline flag using m()', function () {
        $regex = Regine::make()->startOfString()->literal('test')->m()->compile();
        expect($regex)->toBe('/^test/m');
    });

    it('adds dot all flag using s()', function () {
        $regex = Regine::make()->anyChar()->oneOrMore()->s()->compile();
        expect($regex)->toBe('/.+/s');
    });

    it('adds extended flag using x()', function () {
        $regex = Regine::make()->literal('test')->x()->compile();
        expect($regex)->toBe('/test/x');
    });

    it('adds unicode flag using u()', function () {
        $regex = Regine::make()->literal('тест')->u()->compile();
        expect($regex)->toBe('/тест/u');
    });

    it('adds all flags fluently together', function () {
        $regex = Regine::make()
            ->literal('test')
            ->s()
            ->m()
            ->i()
            ->x()
            ->u()
            ->compile();
        expect($regex)->toBe('/test/imsux');
    });
});

describe('WithFlags Method', function () {
    it('sets flags using string array', function () {
        $regex = Regine::make()->literal('test')->withFlags(['i', 'm'])->compile();
        expect($regex)->toBe('/test/im');
    });

    it('sets flags using enum array', function () {
        $regex = Regine::make()
            ->literal('test')
            ->withFlags([RegexFlagsEnum::CASE_INSENSITIVE, RegexFlagsEnum::MULTILINE])
            ->compile();
        expect($regex)->toBe('/test/im');
    });

    it('sets flags using mixed array', function () {
        $regex = Regine::make()
            ->literal('test')
            ->withFlags(['i', RegexFlagsEnum::MULTILINE])
            ->compile();
        expect($regex)->toBe('/test/im');
    });

    it('sets flags using combined string', function () {
        $regex = Regine::make()->literal('test')->withFlags(['ims'])->compile();
        expect($regex)->toBe('/test/ims');
    });

    it('removes duplicates and sorts flags', function () {
        $regex = Regine::make()->literal('test')->withFlags(['mis', 'i'])->compile();
        expect($regex)->toBe('/test/ims');
    });
});

describe('Flag Combinations', function () {
    it('chains multiple flag methods', function () {
        $regex = Regine::make()
            ->literal('test')
            ->caseInsensitive()
            ->multiline()
            ->dotAll()
            ->compile();
        expect($regex)->toBe('/test/ims');
    });

    it('combines withFlags with individual methods', function () {
        $regex = Regine::make()
            ->literal('test')
            ->withFlags(['i'])
            ->multiline()
            ->dotAll()
            ->compile();
        expect($regex)->toBe('/test/ims');
    });

    it('handles all flags at once', function () {
        $regex = Regine::make()
            ->literal('test')
            ->caseInsensitive()
            ->multiline()
            ->dotAll()
            ->extended()
            ->unicode()
            ->compile();
        expect($regex)->toBe('/test/imsux');
    });
});

describe('Flag Management', function () {
    it('checks if flag is set', function () {
        $regine = Regine::make()->literal('test')->caseInsensitive();

        expect($regine->hasFlag(RegexFlagsEnum::CASE_INSENSITIVE))->toBeTrue()
            ->and($regine->hasFlag(RegexFlagsEnum::MULTILINE))->toBeFalse();
    });

    it('gets flags as string', function () {
        $regine = Regine::make()->literal('test')->caseInsensitive()->multiline();

        expect($regine->getFlagsString())->toBe('im');
    });

    it('gets flags as array', function () {
        $regine = Regine::make()->literal('test')->caseInsensitive()->multiline();
        $flags = $regine->getFlags();

        expect($flags)->toHaveCount(2)
            ->and($flags)->toContain(RegexFlagsEnum::CASE_INSENSITIVE)
            ->and($flags)->toContain(RegexFlagsEnum::MULTILINE);
    });

    it('removes specific flag', function () {
        $regex = Regine::make()
            ->literal('test')
            ->caseInsensitive()
            ->multiline()
            ->removeFlag(RegexFlagsEnum::CASE_INSENSITIVE)
            ->compile();
        expect($regex)->toBe('/test/m');
    });

    it('clears all flags', function () {
        $regex = Regine::make()
            ->literal('test')
            ->caseInsensitive()
            ->multiline()
            ->clearFlags()
            ->compile();
        expect($regex)->toBe('/test/');
    });

    it('prevents duplicate flags', function () {
        $regex = Regine::make()
            ->literal('test')
            ->caseInsensitive()
            ->caseInsensitive()
            ->compile();
        expect($regex)->toBe('/test/i');
    });
});

describe('Flag Enum Functionality', function () {
    it('provides correct descriptions', function () {
        expect(RegexFlagsEnum::CASE_INSENSITIVE->getDescription())->toBe('case insensitive matching')
            ->and(RegexFlagsEnum::MULTILINE->getDescription())->toBe('multiline mode (^ and $ match start/end of lines)')
            ->and(RegexFlagsEnum::SINGLE_LINE->getDescription())->toBe('single line mode (. matches newlines)')
            ->and(RegexFlagsEnum::EXTENDED->getDescription())->toBe('extended syntax (allows comments and whitespace)')
            ->and(RegexFlagsEnum::UNICODE->getDescription())->toBe('unicode mode');
    });

    it('gets all available flags', function () {
        $flags = RegexFlagsEnum::getAllFlags();

        expect($flags)->toHaveCount(5)
            ->and($flags)->toHaveKey('i')
            ->and($flags)->toHaveKey('m')
            ->and($flags)->toHaveKey('s')
            ->and($flags)->toHaveKey('x')
            ->and($flags)->toHaveKey('u');
    });

    it('combines flags correctly', function () {
        $flags = [
            RegexFlagsEnum::MULTILINE,
            RegexFlagsEnum::CASE_INSENSITIVE,
            RegexFlagsEnum::SINGLE_LINE,
        ];
        $combined = RegexFlagsEnum::combineFlags($flags);

        expect($combined)->toBe('ims');
    });

    it('parses flag string correctly', function () {
        $parsed = RegexFlagsEnum::parseFlags('ims');

        expect($parsed)->toHaveCount(3)
            ->and($parsed)->toContain(RegexFlagsEnum::CASE_INSENSITIVE)
            ->and($parsed)->toContain(RegexFlagsEnum::MULTILINE)
            ->and($parsed)->toContain(RegexFlagsEnum::SINGLE_LINE);
    });

    it('handles invalid flags gracefully', function () {
        $parsed = RegexFlagsEnum::parseFlags('iqz');

        expect($parsed)->toHaveCount(1)
            ->and($parsed)->toContain(RegexFlagsEnum::CASE_INSENSITIVE);
    });

    it('removes duplicates when parsing', function () {
        $parsed = RegexFlagsEnum::parseFlags('iims');

        expect($parsed)->toHaveCount(3)
            ->and($parsed)->toContain(RegexFlagsEnum::CASE_INSENSITIVE)
            ->and($parsed)->toContain(RegexFlagsEnum::MULTILINE)
            ->and($parsed)->toContain(RegexFlagsEnum::SINGLE_LINE);
    });
});

describe('Integration with Other Components', function () {
    it('works with literals', function () {
        $regex = Regine::make()
            ->literal('Hello World')
            ->caseInsensitive()
            ->compile();
        expect($regex)->toBe('/Hello World/i');
    });

    it('works with character classes', function () {
        $regex = Regine::make()
            ->anyOf('aeiou')
            ->caseInsensitive()
            ->compile();
        expect($regex)->toBe('/[aeiou]/i');
    });

    it('works with quantifiers', function () {
        $regex = Regine::make()
            ->wordChar()
            ->oneOrMore()
            ->caseInsensitive()
            ->compile();
        expect($regex)->toBe('/\w+/i');
    });

    it('works with groups', function () {
        $regex = Regine::make()
            ->group('test')
            ->caseInsensitive()
            ->compile();
        expect($regex)->toBe('/(test)/i');
    });

    it('works with alternation', function () {
        $regex = Regine::make()
            ->orAny(['hello', 'hi'])
            ->caseInsensitive()
            ->compile();
        expect($regex)->toBe('/hello|hi/i');
    });

    it('works with anchors', function () {
        $regex = Regine::make()
            ->startOfString()
            ->literal('test')
            ->endOfString()
            ->multiline()
            ->compile();
        expect($regex)->toBe('/^test$/m');
    });

    it('works with lookarounds', function () {
        $regex = Regine::make()
            ->lookahead('test')
            ->wordChar()
            ->oneOrMore()
            ->caseInsensitive()
            ->compile();
        expect($regex)->toBe('/(?=test)\w+/i');
    });
});

describe('Real-world Patterns with Flags', function () {
    it('creates case-insensitive email validation', function () {
        $regex = Regine::make()
            ->startOfString()
            ->wordChar()
            ->oneOrMore()
            ->literal('@')
            ->wordChar()
            ->oneOrMore()
            ->literal('.')
            ->wordChar()
            ->between(2, 4)
            ->endOfString()
            ->caseInsensitive()
            ->compile();
        expect($regex)->toBe('/^\w+@\w+\.\w{2,4}$/i');
    });

    it('creates multiline text validation', function () {
        $regex = Regine::make()
            ->startOfString()
            ->literal('Chapter')
            ->whitespace()
            ->digit()
            ->oneOrMore()
            ->multiline()
            ->compile();
        expect($regex)->toBe('/^Chapter\s\d+/m');
    });

    it('creates dot-all log parsing', function () {
        $regex = Regine::make()
            ->literal('[ERROR]')
            ->anyChar()
            ->oneOrMore()
            ->literal('[/ERROR]')
            ->dotAll()
            ->compile();
        expect($regex)->toBe('/\[ERROR\].+\[\/ERROR\]/s');
    });

    it('creates unicode-aware name validation', function () {
        $regex = Regine::make()
            ->startOfString()
            ->wordChar()
            ->oneOrMore()
            ->endOfString()
            ->unicode()
            ->compile();
        expect($regex)->toBe('/^\w+$/u');
    });

    it('creates complex pattern with multiple flags', function () {
        $regex = Regine::make()
            ->startOfString()
            ->negativeLookahead('.*[<>]')
            ->anyChar()
            ->oneOrMore()
            ->endOfString()
            ->caseInsensitive()
            ->dotAll()
            ->unicode()
            ->compile();
        expect($regex)->toBe('/^(?!.*[<>]).+$/isu');
    });
});

describe('Debug and Metadata', function () {
    it('includes flags in debug output', function () {
        $regine = Regine::make()->literal('test')->caseInsensitive()->multiline();
        $debug = $regine->debug();

        expect($debug->pattern())->toBe('test')
            ->and($debug->compiled())->toBe('/test/im')
            ->and($debug->flags())->toBe('im');
    });

    it('shows empty flags in debug when no flags set', function () {
        $regine = Regine::make()->literal('test');
        $debug = $regine->debug();

        expect($debug->flags())->toBe('')
            ->and($debug->compiled())->toBe('/test/');
    });
});

describe('Pattern Matching with Flags', function () {
    it('matches case-insensitive patterns', function () {
        $regine = Regine::make()->literal('Test')->caseInsensitive();

        expect($regine->test('test'))->toBeTrue()
            ->and($regine->test('TEST'))->toBeTrue()
            ->and($regine->test('TeSt'))->toBeTrue()
            ->and($regine->test('other'))->toBeFalse();
    });

    it('matches multiline patterns', function () {
        $regine = Regine::make()->startOfString()->literal('test')->multiline();
        $text = "line1\ntest\nline3";

        expect($regine->test($text))->toBeTrue();
    });

    it('matches dot-all patterns', function () {
        $regine = Regine::make()->literal('start')->anyChar()->oneOrMore()->literal('end')->dotAll();
        $text = "start\nsome\nlines\nend";

        expect($regine->test($text))->toBeTrue();
    });

    it('matches unicode patterns', function () {
        $regine = Regine::make()->wordChar()->oneOrMore()->unicode();

        expect($regine->test('тест'))->toBeTrue()
            ->and($regine->test('العربية'))->toBeTrue()
            ->and($regine->test('中文'))->toBeTrue();
    });

    it('combines multiple flag behaviors', function () {
        $regine = Regine::make()
            ->literal('TEST')
            ->anyChar()
            ->oneOrMore()
            ->literal('END')
            ->caseInsensitive()
            ->dotAll();

        $text = "test\nsome\nlines\nend";
        expect($regine->test($text))->toBeTrue();
    });
});

describe('Error Handling and Edge Cases', function () {
    it('handles empty flag string', function () {
        $regex = Regine::make()->literal('test')->withFlags([''])->compile();
        expect($regex)->toBe('/test/');
    });

    it('handles overriding flags', function () {
        $regex = Regine::make()
            ->literal('test')
            ->caseInsensitive()
            ->withFlags(['m']) // This should override previous flags
            ->compile();
        expect($regex)->toBe('/test/m');
    });

    it('maintains flag order consistently', function () {
        $regex1 = Regine::make()->literal('test')->withFlags(['sim'])->compile();
        $regex2 = Regine::make()->literal('test')->withFlags(['mis'])->compile();

        expect($regex1)->toBe('/test/ims');
        expect($regex2)->toBe('/test/ims');
    });

    it('handles method chaining after flags', function () {
        $regex = Regine::make()
            ->literal('start')
            ->caseInsensitive()
            ->literal('end')
            ->compile();
        expect($regex)->toBe('/startend/i');
    });
});
