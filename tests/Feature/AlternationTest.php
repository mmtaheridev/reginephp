<?php

declare(strict_types=1);

use Regine\Exceptions\Alternation\AlternationCallbackDoesntReturnRegine;
use Regine\Exceptions\Alternation\EmptyAlternationException;
use Regine\Regine;

// Basic alternation tests
describe('Basic Alternation', function () {
    it('creates simple alternation with or()', function () {
        $regex = Regine::make()->or('test')->compile();
        expect($regex)->toBe('/test/');
    });

    it('creates multiple alternations with oneOf()', function () {
        $regex = Regine::make()->oneOf(['cat', 'dog', 'bird'])->compile();
        expect($regex)->toBe('/cat|dog|bird/');
    });

    it('handles empty alternatives array', function () {
        Regine::make()->oneOf([]);
    })->throws(EmptyAlternationException::class);
});

// Scoped alternation tests
describe('Scoped Alternation', function () {
    it('creates scoped alternation with closure', function () {
        $regex = Regine::make()
            ->literal('prefix')
            ->alternation(function () {
                return Regine::make()->literal('http')->or('https');
            })
            ->literal('://')
            ->compile();
        expect($regex)->toBe('/prefix(?:http|https)\:\/\//');
    });

    it('creates complex scoped alternation', function () {
        $regex = Regine::make()
            ->startOfString()
            ->alternation(function () {
                return Regine::make()->oneOf(['Mr', 'Mrs', 'Ms', 'Dr']);
            })
            ->literal('.')
            ->optional()
            ->whitespace()
            ->oneOrMore()
            ->wordChar()
            ->oneOrMore()
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?:Mr|Mrs|Ms|Dr)\.?\s+\w+$/');
    });

    it('throws exception for invalid callback return', function () {
        Regine::make()->alternation(function () {
            return 'invalid';
        });
    })->throws(AlternationCallbackDoesntReturnRegine::class);
});

// Quantifier integration tests
describe('Quantifier Integration', function () {
    it('applies quantifiers to alternation correctly', function () {
        $regex = Regine::make()->oneOf(['a', 'b', 'c'])->oneOrMore()->compile();
        expect($regex)->toBe('/(?:a|b|c)+/');
    });

    it('applies quantifiers to simple alternation', function () {
        $regex = Regine::make()->or('test')->optional()->compile();
        expect($regex)->toBe('/(?:test)?/');
    });

    it('applies quantifiers to scoped alternation', function () {
        $regex = Regine::make()
            ->alternation(function () {
                return Regine::make()->oneOf(['http', 'https']);
            })
            ->optional()
            ->compile();
        expect($regex)->toBe('/(?:http|https)?/');
    });

    it('chains quantifiers with alternation', function () {
        $regex = Regine::make()
            ->oneOf(['a', 'b'])
            ->oneOrMore()
            ->literal('-')
            ->oneOf(['x', 'y'])
            ->optional()
            ->compile();
        expect($regex)->toBe('/(?:a|b)+\-(?:x|y)?/');
    });
});

// Integration with other components
describe('Integration with Other Components', function () {
    it('integrates with literals', function () {
        $regex = Regine::make()
            ->literal('start')
            ->oneOf(['middle', 'center'])
            ->literal('end')
            ->compile();
        expect($regex)->toBe('/startmiddle|centerend/');
    });

    it('integrates with character classes', function () {
        $regex = Regine::make()
            ->anyOf('abc')
            ->or('xyz')
            ->compile();
        expect($regex)->toBe('/[abc]|xyz/');
    });

    it('integrates with anchors', function () {
        $regex = Regine::make()
            ->startOfString()
            ->oneOf(['start', 'begin'])
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^start|begin$/');
    });

    it('integrates with groups', function () {
        $regex = Regine::make()
            ->group('test')
            ->or('other')
            ->compile();
        expect($regex)->toBe('/(test)|other/');
    });
});

// Nested Regine object tests
describe('Nested Regine Objects', function () {
    it('auto-compiles nested Regine objects in or()', function () {
        $inner = Regine::make()->digit()->oneOrMore();
        $regex = Regine::make()->or($inner)->compile();
        expect($regex)->toBe('/\d+/');
    });

    it('auto-compiles nested Regine objects in oneOf()', function () {
        $option1 = Regine::make()->literal('http')->literal('://');
        $option2 = Regine::make()->literal('https')->literal('://');
        $regex = Regine::make()->oneOf([$option1, $option2, 'ftp://'])->compile();
        expect($regex)->toBe('/http\:\/\/|https\:\/\/|ftp:///');
    });

    it('handles complex nested patterns in scoped alternation', function () {
        $regex = Regine::make()
            ->literal('prefix')
            ->alternation(function () {
                $email = Regine::make()->wordChar()->oneOrMore()->literal('@')->wordChar()->oneOrMore();
                $phone = Regine::make()->digit()->exactly(3)->literal('-')->digit()->exactly(3)->literal('-')->digit()->exactly(4);

                return Regine::make()->oneOf([$email, $phone]);
            })
            ->literal('suffix')
            ->compile();
        expect($regex)->toBe('/prefix(?:\w+@\w+|\d{3}\-\d{3}\-\d{4})suffix/');
    });
});

// Method chaining tests
describe('Method Chaining', function () {
    it('chains multiple or() calls', function () {
        $regex = Regine::make()->or('a')->or('b')->or('c')->compile();
        expect($regex)->toBe('/a|b|c/');
    });

    it('chains alternation with other methods', function () {
        $regex = Regine::make()
            ->startOfString()
            ->oneOf(['http', 'https'])
            ->literal('://')
            ->wordChar()->oneOrMore()
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^http|https\:\/\/\w+$/');
    });

    it('creates complex patterns with scoped alternation', function () {
        $regex = Regine::make()
            ->startOfString()
            ->alternation(function () {
                return Regine::make()->oneOf(['http', 'https', 'ftp']);
            })
            ->literal('://')
            ->alternation(function () {
                return Regine::make()->oneOf(['www.', 'mail.', 'ftp.']);
            })
            ->optional()
            ->wordChar()->oneOrMore()
            ->literal('.')
            ->wordChar()->between(2, 4)
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?:http|https|ftp)\:\/\/(?:www.|mail.|ftp.)?\w+\.\w{2,4}$/');
    });
});

// Real-world pattern tests
describe('Real-world Patterns', function () {
    it('creates URL protocol pattern', function () {
        $regex = Regine::make()
            ->startOfString()
            ->alternation(function () {
                return Regine::make()->oneOf(['http', 'https', 'ftp', 'ftps']);
            })
            ->literal('://')
            ->anyChar()->oneOrMore()
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?:http|https|ftp|ftps)\:\/\/.+$/');
    });

    it('creates email validation pattern', function () {
        $regex = Regine::make()
            ->startOfString()
            ->wordChar()->oneOrMore()
            ->literal('@')
            ->alternation(function () {
                return Regine::make()->oneOf(['gmail.com', 'yahoo.com', 'outlook.com']);
            })
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^\w+@(?:gmail.com|yahoo.com|outlook.com)$/');
    });

    it('creates file extension pattern', function () {
        $regex = Regine::make()
            ->anyChar()->oneOrMore()
            ->literal('.')
            ->alternation(function () {
                return Regine::make()->oneOf(['jpg', 'jpeg', 'png', 'gif']);
            })
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/.+\.(?:jpg|jpeg|png|gif)$/');
    });

    it('creates boolean value pattern', function () {
        $regex = Regine::make()
            ->startOfString()
            ->alternation(function () {
                return Regine::make()->oneOf(['true', 'false', '1', '0']);
            })
            ->endOfString()
            ->compile();
        expect($regex)->toBe('/^(?:true|false|1|0)$/');
    });
});

// Component metadata and introspection tests
describe('Component Metadata and Introspection', function () {
    it('provides correct metadata for alternation', function () {
        $regine = Regine::make()->oneOf(['cat', 'dog', 'bird']);
        $metadata = $regine->getMetadata();

        expect($metadata['elements'])->toHaveCount(1);
        expect($metadata['elements'][0]['type'])->toBe('alternation');
        expect($metadata['elements'][0]['alternatives'])->toBe(['cat', 'dog', 'bird']);
        expect($metadata['elements'][0]['count'])->toBe(3);
    });

    it('provides correct description for alternation', function () {
        $regine = Regine::make()->oneOf(['cat', 'dog', 'bird']);
        $description = $regine->describe();

        expect($description)->toContain("match any of 'cat', 'dog', 'bird' (3 alternatives)");
    });

    it('provides correct metadata for scoped alternation', function () {
        $regine = Regine::make()
            ->literal('prefix')
            ->alternation(function () {
                return Regine::make()->oneOf(['a', 'b']);
            });

        $metadata = $regine->getMetadata();
        expect($metadata['elements'])->toHaveCount(2);
        expect($metadata['elements'][0]['type'])->toBe('literal');
        expect($metadata['elements'][1]['type'])->toBe('group');
        expect($metadata['elements'][1]['group_type'])->toBe('NON_CAPTURING');
    });

    it('includes alternation in debug output', function () {
        $regine = Regine::make()->oneOf(['test', 'other']);
        $debug = $regine->debug();

        expect($debug->pattern())->toBe('test|other');
        expect($debug->compiled())->toBe('/test|other/');
        expect($debug->elementCount())->toBe(1);
    });
});

// Pattern matching tests
describe('Pattern Matching', function () {
    it('matches alternation patterns correctly', function () {
        $regine = Regine::make()->oneOf(['cat', 'dog', 'bird']);

        expect($regine->test('cat'))->toBe(true);
        expect($regine->test('dog'))->toBe(true);
        expect($regine->test('bird'))->toBe(true);
        expect($regine->test('fish'))->toBe(false);
    });

    it('matches scoped alternation patterns', function () {
        $regine = Regine::make()
            ->literal('prefix')
            ->alternation(function () {
                return Regine::make()->oneOf(['http', 'https']);
            })
            ->literal('://');

        expect($regine->test('prefixhttp://'))->toBe(true);
        expect($regine->test('prefixhttps://'))->toBe(true);
        expect($regine->test('prefixftp://'))->toBe(false);
    });

    it('extracts matches from alternation patterns', function () {
        $regine = Regine::make()->oneOf(['cat', 'dog', 'bird']);

        $matches = $regine->matches('I have a cat');
        expect($matches[0])->toBe('cat');

        $matches = $regine->matches('My dog is happy');
        expect($matches[0])->toBe('dog');
    });
});
