<?php

declare(strict_types=1);

use Regine\Components\GroupComponent;
use Regine\Enums\GroupTypesEnum;
use Regine\Exceptions\Group\ConditionalGroupWithNoConditionException;
use Regine\Exceptions\Group\ConditionForUncoditionalGroupException;
use Regine\Exceptions\Group\EmptyGroupPatternException;
use Regine\Exceptions\Group\InvalidGroupNameException;
use Regine\Exceptions\Group\NameForUnnamedGroupException;
use Regine\Exceptions\Group\NoNameForNamedGroupException;
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
        expect($component->canBeQuantified())->toBeTrue();
    });

    it('provides correct metadata for capturing group', function () {
        $component = new GroupComponent(GroupTypesEnum::CAPTURING, 'abc');
        $metadata = $component->getMetadata();

        expect($metadata['type'])->toBe('group')
            ->and($metadata['group_type'])->toBe('CAPTURING')
            ->and($metadata['pattern'])->toBe('abc')
            ->and($metadata['name'])->toBeNull()
            ->and($metadata['condition'])->toBeNull()
            ->and($metadata['else_pattern'])->toBeNull();
    });

    it('provides correct metadata for named group', function () {
        $component = new GroupComponent(GroupTypesEnum::NAMED, 'abc', 'test');
        $metadata = $component->getMetadata();

        expect($metadata['group_type'])->toBe('NAMED')
            ->and($metadata['name'])->toBe('test');
    });

    it('provides correct metadata for conditional group', function () {
        $component = new GroupComponent(GroupTypesEnum::CONDITIONAL, 'yes', null, '1', 'no');
        $metadata = $component->getMetadata();

        expect($metadata['group_type'])->toBe('CONDITIONAL')
            ->and($metadata['condition'])->toBe('1')
            ->and($metadata['else_pattern'])->toBe('no');
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
            ->toThrow(NoNameForNamedGroupException::class);
    });

    it('throws exception for invalid group name', function () {
        expect(fn () => new GroupComponent(GroupTypesEnum::NAMED, 'abc', '123invalid'))
            ->toThrow(InvalidGroupNameException::class);
    });

    it('throws exception for conditional group without condition', function () {
        expect(fn () => new GroupComponent(GroupTypesEnum::CONDITIONAL, 'abc'))
            ->toThrow(ConditionalGroupWithNoConditionException::class);
    });

    it('throws exception for empty pattern', function () {
        expect(fn () => new GroupComponent(GroupTypesEnum::CAPTURING, ''))
            ->toThrow(EmptyGroupPatternException::class);
    });

    it('throws exception for non-named group with name', function () {
        expect(fn () => new GroupComponent(GroupTypesEnum::CAPTURING, 'abc', 'test'))
            ->toThrow(NameForUnnamedGroupException::class);
    });

    it('throws exception for non-conditional group with condition', function () {
        expect(fn () => new GroupComponent(GroupTypesEnum::CAPTURING, 'abc', null, '1'))
            ->toThrow(ConditionForUncoditionalGroupException::class);
    });

    it('accepts valid group names', function () {
        $validNames = ['test', 'test123', 'test_123', '_test', 'Test', 'TEST'];

        foreach ($validNames as $name) {
            $component = new GroupComponent(GroupTypesEnum::NAMED, 'abc', $name);
            expect($component->compile())->toBe("(?<{$name}>abc)");
        }
    });

    it('rejects invalid group names', function () {
        $invalidNames = ['123test', 'test-123', 'test.123', 'test@123'];

        foreach ($invalidNames as $key => $name) {
            expect(fn () => new GroupComponent(
                type: GroupTypesEnum::NAMED,
                pattern: 'abc',
                name: $name,
            ))->toThrow(InvalidGroupNameException::class);
        }

        expect(fn () => new GroupComponent(
            type: GroupTypesEnum::NAMED,
            pattern: 'abc',
            name: '',
        ))->toThrow(NoNameForNamedGroupException::class);
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
