<?php

declare(strict_types=1);

use Regine\Regine;

describe('Positive Lookahead', function () {
    it('creates positive lookahead with string pattern', function () {
        $regex = Regine::make()->literal('foo')->lookahead('bar')->compile();
        expect($regex)->toBe('/foo(?=bar)/');
    });

    it('creates positive lookahead with Regine pattern', function () {
        $pattern = Regine::make()->digit()->oneOrMore();
        $regex = Regine::make()->literal('test')->lookahead($pattern)->compile();
        expect($regex)->toBe('/test(?=\d+)/');
    });

    it('creates standalone positive lookahead', function () {
        $regex = Regine::make()->lookahead('test')->compile();
        expect($regex)->toBe('/(?=test)/');
    });

    it('creates multiple positive lookaheads', function () {
        $regex = Regine::make()
            ->lookahead('foo')
            ->lookahead('bar')
            ->anyChar()
            ->oneOrMore()
            ->compile();
        expect($regex)->toBe('/(?=foo)(?=bar).+/');
    });

    it('integrates with other components', function () {
        $regex = Regine::make()
            ->startOfString()
            ->lookahead('test')
            ->wordChar()
            ->oneOrMore()
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?=test)\w+$/');
    });
});

describe('Negative Lookahead', function () {
    it('creates negative lookahead with string pattern', function () {
        $regex = Regine::make()->literal('foo')->negativeLookahead('bar')->compile();
        expect($regex)->toBe('/foo(?!bar)/');
    });

    it('creates negative lookahead with Regine pattern', function () {
        $pattern = Regine::make()->digit()->oneOrMore();
        $regex = Regine::make()->literal('test')->negativeLookahead($pattern)->compile();
        expect($regex)->toBe('/test(?!\d+)/');
    });

    it('creates standalone negative lookahead', function () {
        $regex = Regine::make()->negativeLookahead('test')->compile();
        expect($regex)->toBe('/(?!test)/');
    });

    it('creates multiple negative lookaheads', function () {
        $regex = Regine::make()
            ->negativeLookahead('foo')
            ->negativeLookahead('bar')
            ->anyChar()
            ->oneOrMore()
            ->compile();
        expect($regex)->toBe('/(?!foo)(?!bar).+/');
    });

    it('integrates with other components', function () {
        $regex = Regine::make()
            ->startOfString()
            ->negativeLookahead('test')
            ->wordChar()
            ->oneOrMore()
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?!test)\w+$/');
    });
});

describe('Positive Lookbehind', function () {
    it('creates positive lookbehind with string pattern', function () {
        $regex = Regine::make()->lookbehind('foo')->literal('bar')->compile();
        expect($regex)->toBe('/(?<=foo)bar/');
    });

    it('creates positive lookbehind with Regine pattern', function () {
        $pattern = Regine::make()->digit()->oneOrMore();
        $regex = Regine::make()->lookbehind($pattern)->literal('test')->compile();
        expect($regex)->toBe('/(?<=\d+)test/');
    });

    it('creates standalone positive lookbehind', function () {
        $regex = Regine::make()->lookbehind('test')->compile();
        expect($regex)->toBe('/(?<=test)/');
    });

    it('creates multiple positive lookbehinds', function () {
        $regex = Regine::make()
            ->lookbehind('foo')
            ->lookbehind('bar')
            ->anyChar()
            ->oneOrMore()
            ->compile();
        expect($regex)->toBe('/(?<=foo)(?<=bar).+/');
    });

    it('integrates with other components', function () {
        $regex = Regine::make()
            ->startOfString()
            ->lookbehind('test')
            ->wordChar()
            ->oneOrMore()
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?<=test)\w+$/');
    });
});

describe('Negative Lookbehind', function () {
    it('creates negative lookbehind with string pattern', function () {
        $regex = Regine::make()->negativeLookbehind('foo')->literal('bar')->compile();
        expect($regex)->toBe('/(?<!foo)bar/');
    });

    it('creates negative lookbehind with Regine pattern', function () {
        $pattern = Regine::make()->digit()->oneOrMore();
        $regex = Regine::make()->negativeLookbehind($pattern)->literal('test')->compile();
        expect($regex)->toBe('/(?<!\d+)test/');
    });

    it('creates standalone negative lookbehind', function () {
        $regex = Regine::make()->negativeLookbehind('test')->compile();
        expect($regex)->toBe('/(?<!test)/');
    });

    it('creates multiple negative lookbehinds', function () {
        $regex = Regine::make()
            ->negativeLookbehind('foo')
            ->negativeLookbehind('bar')
            ->anyChar()
            ->oneOrMore()
            ->compile();
        expect($regex)->toBe('/(?<!foo)(?<!bar).+/');
    });

    it('integrates with other components', function () {
        $regex = Regine::make()
            ->startOfString()
            ->negativeLookbehind('test')
            ->wordChar()
            ->oneOrMore()
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?<!test)\w+$/');
    });
});

describe('Mixed Lookarounds', function () {
    it('combines different types of lookarounds', function () {
        $regex = Regine::make()
            ->lookbehind('pre')
            ->lookahead('post')
            ->literal('test')
            ->compile();
        expect($regex)->toBe('/(?<=pre)(?=post)test/');
    });

    it('creates complex lookaround patterns', function () {
        $regex = Regine::make()
            ->negativeLookbehind('no')
            ->lookahead('yes')
            ->negativeLookahead('maybe')
            ->wordChar()
            ->oneOrMore()
            ->compile();
        expect($regex)->toBe('/(?<!no)(?=yes)(?!maybe)\w+/');
    });

    it('integrates with groups', function () {
        $regex = Regine::make()
            ->group('test')
            ->lookahead('next')
            ->compile();
        expect($regex)->toBe('/(test)(?=next)/');
    });

    it('integrates with alternation', function () {
        $regex = Regine::make()
            ->lookahead('test')
            ->orAny(['foo', 'bar'])
            ->compile();
        expect($regex)->toBe('/(?=test)foo|bar/');
    });

    it('integrates with character classes', function () {
        $regex = Regine::make()
            ->lookbehind('pre')
            ->anyOf('abc')
            ->lookahead('post')
            ->compile();
        expect($regex)->toBe('/(?<=pre)[abc](?=post)/');
    });
});

describe('Real-world Patterns', function () {
    it('creates password validation with lookaheads', function () {
        $regex = Regine::make()
            ->startOfString()
            ->lookahead('.*[a-z]')
            ->lookahead('.*[A-Z]')
            ->lookahead('.*\d')
            ->lookahead('.*[@$!%*?&]')
            ->anyChar()
            ->atLeast(8)
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/');
    });

    it('creates email validation with lookaheads', function () {
        $regex = Regine::make()
            ->startOfString()
            ->lookahead('.*@')
            ->lookahead('.*\.')
            ->negativeLookahead('.*@.*@')
            ->wordChar()
            ->oneOrMore()
            ->literal('@')
            ->wordChar()
            ->oneOrMore()
            ->literal('.')
            ->wordChar()
            ->between(2, 4)
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?=.*@)(?=.*\.)(?!.*@.*@)\w+@\w+\.\w{2,4}$/');
    });

    it('creates word boundary with lookarounds', function () {
        $regex = Regine::make()
            ->negativeLookbehind('\w')
            ->literal('word')
            ->negativeLookahead('\w')
            ->compile();
        expect($regex)->toBe('/(?<!\w)word(?!\w)/');
    });

    it('creates number validation with lookaheads', function () {
        $regex = Regine::make()
            ->startOfString()
            ->negativeLookahead('0+$')
            ->digit()
            ->oneOrMore()
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?!0+$)\d+$/');
    });

    it('creates filename validation with lookaheads', function () {
        $regex = Regine::make()
            ->startOfString()
            ->negativeLookahead('.*[<>:"/\\|?*]')
            ->lookahead('.*\.')
            ->negativeLookahead('.*\.$')
            ->anyChar()
            ->oneOrMore()
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?!.*[<>:"/\\|?*])(?=.*\.)(?!.*\.$).+$/');
    });
});

describe('Component Metadata and Introspection', function () {
    it('provides correct metadata for lookaround components', function () {
        $regine = Regine::make()->lookahead('test');
        $metadata = $regine->getMetadata();

        expect($metadata['elements'])->toHaveCount(1);
        expect($metadata['elements'][0]['type'])->toBe('lookaround');
        expect($metadata['elements'][0]['lookaround_type'])->toBe('positive_lookahead');
        expect($metadata['elements'][0]['assertion_content']['pattern'])->toBe('test');
    });

    it('provides correct description for lookaround components', function () {
        $regine = Regine::make()->negativeLookahead('test');
        $description = $regine->describe();

        expect($description)->toContain("negative lookahead assertion for 'Raw pattern: 'test''");
    });

    it('provides correct debug information', function () {
        $regine = Regine::make()->lookbehind('pre')->literal('test');
        $debug = $regine->debug();

        expect($debug->pattern())->toBe('(?<=pre)test');
        expect($debug->compiled())->toBe('/(?<=pre)test/');
        expect($debug->elementCount())->toBe(2);
    });

    it('handles complex patterns in metadata', function () {
        $pattern = Regine::make()->digit()->oneOrMore();
        $regine = Regine::make()->lookahead($pattern);
        $metadata = $regine->getMetadata();

        expect($metadata['elements'][0]['assertion_content']['pattern'])->toBe('\d+');
    });
});

describe('Pattern Matching', function () {
    it('matches positive lookahead patterns correctly', function () {
        $regine = Regine::make()->literal('foo')->lookahead('bar');

        expect($regine->test('foobar'))->toBe(true);
        expect($regine->test('foo'))->toBe(false);
        expect($regine->test('foobaz'))->toBe(false);
    });

    it('matches negative lookahead patterns correctly', function () {
        $regine = Regine::make()->literal('foo')->negativeLookahead('bar');

        expect($regine->test('foo'))->toBe(true);
        expect($regine->test('foobaz'))->toBe(true);
        expect($regine->test('foobar'))->toBe(false);
    });

    it('matches positive lookbehind patterns correctly', function () {
        $regine = Regine::make()->lookbehind('foo')->literal('bar');

        expect($regine->test('foobar'))->toBe(true);
        expect($regine->test('bar'))->toBe(false);
        expect($regine->test('bazbar'))->toBe(false);
    });

    it('matches negative lookbehind patterns correctly', function () {
        $regine = Regine::make()->negativeLookbehind('foo')->literal('bar');

        expect($regine->test('bar'))->toBe(true);
        expect($regine->test('bazbar'))->toBe(true);
        expect($regine->test('foobar'))->toBe(false);
    });

    it('matches complex lookaround combinations', function () {
        // Create a pattern that matches "test" with "pre" before it and "post" after it
        $regine = Regine::make()
            ->lookbehind('pre')
            ->literal('test')
            ->lookahead('post');

        expect($regine->test('pretestpost'))->toBe(true);
        expect($regine->test('pretest'))->toBe(false);
        expect($regine->test('testpost'))->toBe(false);
        expect($regine->test('test'))->toBe(false);
    });
});

describe('Error Handling', function () {
    it('handles lookarounds that cannot be quantified', function () {
        $regine = Regine::make()->lookahead('test');
        $metadata = $regine->getMetadata();

        expect($metadata['elements'][0]['type'])->toBe('lookaround');
        // Lookarounds can be quantified in the new architecture
        expect($metadata['elements'][0]['can_be_quantified'])->toBe(true);
    });

    it('handles empty patterns gracefully', function () {
        $regex = Regine::make()->lookahead('')->compile();
        expect($regex)->toBe('/(?=)/');
    });

    it('handles complex nested patterns', function () {
        $nested = Regine::make()
            ->group('test')
            ->orAny(['foo', 'bar'])
            ->digit()
            ->oneOrMore();

        $regex = Regine::make()->lookahead($nested)->compile();
        expect($regex)->toBe('/(?=(test)foo|bar\d+)/');
    });
});
