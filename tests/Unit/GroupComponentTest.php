<?php

declare(strict_types=1);

use Regine\Components\GroupComponent;
use Regine\Enums\GroupTypesEnum;
use Regine\Regine;

// Basic group component tests
describe('GroupComponent', function () {
    it('creates a capturing group', function () {
        $component = new GroupComponent(GroupTypesEnum::CAPTURING, 'abc');
        expect($component->compile())->toBe('(abc)');
    });

    it('creates a non-capturing group', function () {
        $component = new GroupComponent(GroupTypesEnum::NON_CAPTURING, 'abc');
        expect($component->compile())->toBe('(?:abc)');
    });

    it('creates a named group', function () {
        $component = new GroupComponent(GroupTypesEnum::NAMED, 'abc', 'test');
        expect($component->compile())->toBe('(?<test>abc)');
    });

    it('creates an atomic group', function () {
        $component = new GroupComponent(GroupTypesEnum::ATOMIC, 'abc');
        expect($component->compile())->toBe('(?>abc)');
    });

    it('creates a conditional group with else', function () {
        $component = new GroupComponent(GroupTypesEnum::CONDITIONAL, 'yes', null, '1', 'no');
        expect($component->compile())->toBe('(?(1)yes|no)');
    });

    it('creates a conditional group without else', function () {
        $component = new GroupComponent(GroupTypesEnum::CONDITIONAL, 'yes', null, '1');
        expect($component->compile())->toBe('(?(1)yes)');
    });

    it('auto-compiles Regine objects', function () {
        $regine = Regine::make()->literal('test');
        $component = new GroupComponent(GroupTypesEnum::CAPTURING, $regine);
        expect($component->compile())->toBe('(test)');
    });

    it('handles complex nested Regine objects', function () {
        $regine = Regine::make()->digit()->oneOrMore()->literal('@')->wordChar()->oneOrMore();
        $component = new GroupComponent(GroupTypesEnum::CAPTURING, $regine);
        expect($component->compile())->toBe('(\d+@\w+)');
    });

    it('returns correct type', function () {
        $component = new GroupComponent(GroupTypesEnum::CAPTURING, 'abc');
        expect($component->getType())->toBe('group');
    });

    it('can be quantified', function () {
        $component = new GroupComponent(GroupTypesEnum::CAPTURING, 'abc');
        expect($component->canBeQuantified())->toBe(true);
    });

    it('provides correct metadata for capturing group', function () {
        $component = new GroupComponent(GroupTypesEnum::CAPTURING, 'abc');
        $metadata = $component->getMetadata();

        expect($metadata['type'])->toBe('group');
        expect($metadata['group_type'])->toBe('CAPTURING');
        expect($metadata['pattern'])->toBe('abc');
        expect($metadata['name'])->toBeNull();
        expect($metadata['condition'])->toBeNull();
        expect($metadata['else_pattern'])->toBeNull();
    });

    it('provides correct metadata for named group', function () {
        $component = new GroupComponent(GroupTypesEnum::NAMED, 'abc', 'test');
        $metadata = $component->getMetadata();

        expect($metadata['group_type'])->toBe('NAMED');
        expect($metadata['name'])->toBe('test');
    });

    it('provides correct metadata for conditional group', function () {
        $component = new GroupComponent(GroupTypesEnum::CONDITIONAL, 'yes', null, '1', 'no');
        $metadata = $component->getMetadata();

        expect($metadata['group_type'])->toBe('CONDITIONAL');
        expect($metadata['condition'])->toBe('1');
        expect($metadata['else_pattern'])->toBe('no');
    });

    it('provides correct description for capturing group', function () {
        $component = new GroupComponent(GroupTypesEnum::CAPTURING, 'abc');
        expect($component->getDescription())->toBe("capturing group containing 'abc'");
    });

    it('provides correct description for named group', function () {
        $component = new GroupComponent(GroupTypesEnum::NAMED, 'abc', 'test');
        expect($component->getDescription())->toBe("named group named 'test' containing 'abc'");
    });

    it('provides correct description for conditional group', function () {
        $component = new GroupComponent(GroupTypesEnum::CONDITIONAL, 'yes', null, '1');
        expect($component->getDescription())->toBe("conditional group with condition '1' containing 'yes'");
    });
});

// Error handling tests
describe('GroupComponent Error Handling', function () {
    it('throws exception for named group without name', function () {
        expect(fn () => new GroupComponent(GroupTypesEnum::NAMED, 'abc'))
            ->toThrow(InvalidArgumentException::class, 'Named group requires a name.');
    });

    it('throws exception for invalid group name', function () {
        expect(fn () => new GroupComponent(GroupTypesEnum::NAMED, 'abc', '123invalid'))
            ->toThrow(InvalidArgumentException::class, 'Invalid group name');
    });

    it('throws exception for conditional group without condition', function () {
        expect(fn () => new GroupComponent(GroupTypesEnum::CONDITIONAL, 'abc'))
            ->toThrow(InvalidArgumentException::class, 'Conditional group requires a condition.');
    });

    it('throws exception for empty pattern', function () {
        expect(fn () => new GroupComponent(GroupTypesEnum::CAPTURING, ''))
            ->toThrow(InvalidArgumentException::class, 'Group pattern cannot be empty.');
    });

    it('throws exception for non-named group with name', function () {
        expect(fn () => new GroupComponent(GroupTypesEnum::CAPTURING, 'abc', 'test'))
            ->toThrow(InvalidArgumentException::class, 'Only named groups can have a name.');
    });

    it('throws exception for non-conditional group with condition', function () {
        expect(fn () => new GroupComponent(GroupTypesEnum::CAPTURING, 'abc', null, '1'))
            ->toThrow(InvalidArgumentException::class, 'Only conditional groups can have conditions');
    });

    it('accepts valid group names', function () {
        $validNames = ['test', 'test123', 'test_123', '_test', 'Test', 'TEST'];

        foreach ($validNames as $name) {
            $component = new GroupComponent(GroupTypesEnum::NAMED, 'abc', $name);
            expect($component->compile())->toBe("(?<{$name}>abc)");
        }
    });

    it('rejects invalid group names', function () {
        $invalidNames = ['123test', 'test-123', 'test.123', 'test@123', ''];

        foreach ($invalidNames as $name) {
            expect(fn () => new GroupComponent(GroupTypesEnum::NAMED, 'abc', $name))
                ->toThrow(InvalidArgumentException::class);
        }
    });
});

// Auto-compilation tests
describe('GroupComponent Auto-compilation', function () {
    it('auto-compiles nested Regine objects', function () {
        $inner = Regine::make()->literal('test')->digit()->oneOrMore();
        $component = new GroupComponent(GroupTypesEnum::CAPTURING, $inner);
        expect($component->compile())->toBe('(test\d+)');
    });

    it('auto-compiles Regine objects in conditional groups', function () {
        $thenPattern = Regine::make()->literal('yes');
        $elsePattern = Regine::make()->literal('no');
        $component = new GroupComponent(GroupTypesEnum::CONDITIONAL, $thenPattern, null, '1', $elsePattern);
        expect($component->compile())->toBe('(?(1)yes|no)');
    });

    it('handles deeply nested Regine objects', function () {
        $inner = Regine::make()
            ->startOfString()
            ->digit()->oneOrMore()
            ->literal('@')
            ->wordChar()->oneOrMore()
            ->literal('.')
            ->wordChar()->between(2, 4)
            ->endOfString();

        $component = new GroupComponent(GroupTypesEnum::CAPTURING, $inner);
        expect($component->compile())->toBe('(^\d+@\w+\.\w{2,4}$)');
    });
});
