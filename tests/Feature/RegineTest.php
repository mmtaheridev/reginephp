<?php

declare(strict_types=1);

use Regine\Regine;

// Basic functionality tests
describe('Basic Regine Functionality', function () {
    it('creates a new instance with make', function () {
        expect(Regine::make())->toBeInstanceOf(Regine::class);
    });

    it('compiles to basic regex format', function () {
        $regex = Regine::make()->compile();
        expect($regex)->toBe('//');
    });

    it('handles empty pattern compilation', function () {
        $regine = Regine::make();
        expect($regine->compile())->toBe('//');
    });
});

// Integration tests that combine multiple components
describe('Complex Pattern Integration', function () {
    it('creates complex email validation pattern', function () {
        $regex = Regine::make()
            ->startOfString()
            ->wordChar()->oneOrMore()
            ->anyOf('.-_')->optional()
            ->wordChar()->zeroOrMore()
            ->literal('@')
            ->wordChar()->oneOrMore()
            ->literal('.')
            ->wordChar()->between(2, 4)
            ->endOfString()
            ->compile();

        expect($regex)->toBe('/^\w+[.\-_]?\w*@\w+\.\w{2,4}$/');
    });

    it('creates complex phone number validation pattern', function () {
        $regex = Regine::make()
            ->startOfString()
            ->literal('(')->optional()
            ->digit()->exactly(3)
            ->literal(')')->optional()
            ->anyOf(' -')->optional()
            ->digit()->exactly(3)
            ->anyOf(' -')
            ->digit()->exactly(4)
            ->endOfString()
            ->compile();

        expect($regex)->toBe('/^\(?\d{3}\)?[ \-]?\d{3}[ \-]\d{4}$/');
    });

    it('creates complex password validation pattern', function () {
        $regex = Regine::make()
            ->startOfString()
            ->anyOf('A-Za-z0-9!@#$%^&*')->between(8, 32)
            ->endOfString()
            ->compile();

        expect($regex)->toBe('/^[A\-Za\-z0\-9!@#$%\^&*]{8,32}$/');
    });

    it('creates URL validation pattern', function () {
        $regex = Regine::make()
            ->startOfString()
            ->literal('http')
            ->literal('s')->optional()
            ->literal('://')
            ->wordChar()->oneOrMore()
            ->literal('.')
            ->wordChar()->between(2, 4)
            ->endOfString()
            ->compile();

        expect($regex)->toBe('/^https?\:\/\/\w+\.\w{2,4}$/');
    });

    it('creates date validation pattern', function () {
        $regex = Regine::make()
            ->startOfString()
            ->digit()->exactly(4)
            ->literal('-')
            ->digit()->exactly(2)
            ->literal('-')
            ->digit()->exactly(2)
            ->endOfString()
            ->compile();

        expect($regex)->toBe('/^\d{4}\-\d{2}\-\d{2}$/');
    });

    it('creates IP address validation pattern', function () {
        $regex = Regine::make()
            ->startOfString()
            ->digit()->between(1, 3)
            ->literal('.')
            ->digit()->between(1, 3)
            ->literal('.')
            ->digit()->between(1, 3)
            ->literal('.')
            ->digit()->between(1, 3)
            ->endOfString()
            ->compile();

        expect($regex)->toBe('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/');
    });

    it('creates username validation pattern', function () {
        $regex = Regine::make()
            ->startOfString()
            ->wordChar()->between(3, 20)
            ->endOfString()
            ->compile();

        expect($regex)->toBe('/^\w{3,20}$/');
    });

    it('creates markdown header pattern', function () {
        $regex = Regine::make()
            ->startOfLine()
            ->literal('#')
            ->literal(' ')
            ->anyChar()->oneOrMore()
            ->compile();

        expect($regex)->toBe('/^# .+/');
    });

    it('creates file extension pattern', function () {
        $regex = Regine::make()
            ->anyChar()->oneOrMore()
            ->literal('.')
            ->wordChar()->between(2, 4)
            ->endOfString()
            ->compile();

        expect($regex)->toBe('/.+\.\w{2,4}$/');
    });

    it('creates HTML tag pattern', function () {
        $regex = Regine::make()
            ->literal('<')
            ->wordChar()->oneOrMore()
            ->literal('>')
            ->anyChar()->zeroOrMore()
            ->literal('</')
            ->wordChar()->oneOrMore()
            ->literal('>')
            ->compile();

        expect($regex)->toBe('/\<\w+\>.*\<\/\w+\>/');
    });
});

// Fluent interface tests
describe('Fluent Interface', function () {
    it('supports fluent interface chaining', function () {
        $regex = Regine::make()
            ->literal('test')
            ->digit()
            ->oneOrMore()
            ->literal('end')
            ->compile();

        expect($regex)->toBe('/test\d+end/');
    });

    it('handles long method chains', function () {
        $regex = Regine::make()
            ->startOfString()
            ->literal('start')
            ->digit()->exactly(3)
            ->literal('-')
            ->wordChar()->oneOrMore()
            ->literal('.')
            ->letter()->between(2, 4)
            ->literal('_')
            ->anyChar()->optional()
            ->endOfString()
            ->compile();

        expect($regex)->toBe('/^start\d{3}\-\w+\.[a-zA-Z]{2,4}_.?$/');
    });

    it('handles multiple consecutive quantifiers', function () {
        $regex = Regine::make()
            ->digit()->oneOrMore()
            ->literal('.')
            ->digit()->zeroOrMore()
            ->literal('e')
            ->anyOf('+-')->optional()
            ->digit()->atLeast(1)
            ->compile();

        expect($regex)->toBe('/\d+\.\d*e[+\-]?\d{1,}/');
    });

    it('handles mixed anchors and boundaries', function () {
        $regex = Regine::make()
            ->startOfString()
            ->wordBoundary()
            ->literal('word')
            ->wordBoundary()
            ->endOfString()
            ->compile();

        expect($regex)->toBe('/^\bword\b$/');
    });

    it('handles pattern with all component types', function () {
        $regex = Regine::make()
            ->startOfString()                    // Anchor
            ->literal('prefix')                  // Literal
            ->digit()->exactly(3)               // Shorthand + Quantifier
            ->anyOf('abc')->optional()          // Character class + Quantifier
            ->wordBoundary()                    // Anchor
            ->endOfString()                     // Anchor
            ->compile();

        expect($regex)->toBe('/^prefix\d{3}[abc]?\b$/');
    });
});

// Group functionality tests
describe('Groups', function () {
    it('creates capturing groups', function () {
        $regex = Regine::make()->group('abc')->compile();
        expect($regex)->toBe('/(abc)/');
    });

    it('creates non-capturing groups', function () {
        $regex = Regine::make()->nonCapturingGroup('abc')->compile();
        expect($regex)->toBe('/(?:abc)/');
    });

    it('creates named groups', function () {
        $regex = Regine::make()->namedGroup('test', 'abc')->compile();
        expect($regex)->toBe('/(?<test>abc)/');
    });

    it('creates atomic groups', function () {
        $regex = Regine::make()->atomicGroup('abc')->compile();
        expect($regex)->toBe('/(?>abc)/');
    });

    it('creates conditional groups with else', function () {
        $regex = Regine::make()->conditionalGroup('1', 'yes', 'no')->compile();
        expect($regex)->toBe('/(?(1)yes|no)/');
    });

    it('creates conditional groups without else', function () {
        $regex = Regine::make()->conditionalGroup('1', 'yes')->compile();
        expect($regex)->toBe('/(?(1)yes)/');
    });

    it('auto-compiles nested Regine objects in groups', function () {
        $inner = Regine::make()->digit()->oneOrMore()->literal('@')->wordChar()->oneOrMore();
        $regex = Regine::make()->group($inner)->compile();
        expect($regex)->toBe('/(\d+@\w+)/');
    });

    it('chains groups with other components', function () {
        $regex = Regine::make()
            ->startOfString()
            ->group('abc')
            ->literal('-')
            ->nonCapturingGroup('def')
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(abc)\-(?:def)$/');
    });

    it('handles deeply nested groups', function () {
        $inner = Regine::make()->digit()->oneOrMore();
        $middle = Regine::make()->nonCapturingGroup($inner);
        $regex = Regine::make()->group($middle)->compile();
        expect($regex)->toBe('/((?:\d+))/');
    });

    it('applies quantifiers to groups', function () {
        $regex = Regine::make()->group('abc')->oneOrMore()->compile();
        expect($regex)->toBe('/(abc)+/');
    });

    it('creates complex email-like pattern with groups', function () {
        $username = Regine::make()->wordChar()->oneOrMore();
        $domain = Regine::make()->wordChar()->oneOrMore();
        $tld = Regine::make()->wordChar()->between(2, 4);

        $regex = Regine::make()
            ->startOfString()
            ->namedGroup('username', $username)
            ->literal('@')
            ->namedGroup('domain', $domain)
            ->literal('.')
            ->namedGroup('tld', $tld)
            ->endOfString()
            ->compile();

        expect($regex)->toBe('/^(?<username>\w+)@(?<domain>\w+)\.(?<tld>\w{2,4})$/');
    });

    it('handles alternation with groups', function () {
        $option1 = Regine::make()->literal('http');
        $option2 = Regine::make()->literal('https');
        $regex = Regine::make()
            ->nonCapturingGroup($option1)
            ->literal('|')
            ->nonCapturingGroup($option2)
            ->compile();
        expect($regex)->toBe('/(?:http)\|(?:https)/');
    });

    it('works with conditional groups using group references', function () {
        $regex = Regine::make()
            ->group('test')
            ->conditionalGroup('1', 'found', 'not found')
            ->compile();
        expect($regex)->toBe('/(test)(?(1)found|not found)/');
    });

    it('maintains component count and metadata', function () {
        $regine = Regine::make()
            ->group('abc')
            ->namedGroup('test', 'def');

        expect($regine->getComponentCount())->toBe(2);

        $metadata = $regine->getMetadata();
        expect($metadata[0]['type'])->toBe('group');
        expect($metadata[0]['group_type'])->toBe('CAPTURING');
        expect($metadata[1]['type'])->toBe('group');
        expect($metadata[1]['group_type'])->toBe('NAMED');
        expect($metadata[1]['name'])->toBe('test');
    });

    it('provides correct descriptions for groups', function () {
        $regine = Regine::make()
            ->group('abc')
            ->namedGroup('test', 'def');

        expect($regine->describe())->toContain("capturing group containing 'abc'");
        expect($regine->describe())->toContain("named group named 'test' containing 'def'");
    });

    it('tests actual matching with groups', function () {
        $regine = Regine::make()
            ->startOfString()
            ->namedGroup('word', '\w+')
            ->literal(' ')
            ->namedGroup('number', '\d+')
            ->endOfString();

        expect($regine->test('hello 123'))->toBe(true);
        expect($regine->test('hello abc'))->toBe(false);

        $matches = $regine->matches('hello 123');
        expect($matches)->toHaveCount(5); // Full match + 2 named groups + 2 numeric groups
        expect($matches[0])->toBe('hello 123'); // Full match
        expect($matches['word'])->toBe('hello'); // Named group
        expect($matches[1])->toBe('hello'); // Same as named group
        expect($matches['number'])->toBe('123'); // Named group
        expect($matches[2])->toBe('123'); // Same as named group
    });
});
