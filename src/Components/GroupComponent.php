<?php

declare(strict_types=1);

namespace Regine\Components;

use Regine\Contracts\RegexComponent;
use Regine\Enums\GroupTypesEnum;
use Regine\Exceptions\Group\ConditionalGroupWithNoConditionException;
use Regine\Exceptions\Group\ConditionForUncoditionalGroupException;
use Regine\Exceptions\Group\EmptyGroupPatternException;
use Regine\Exceptions\Group\InvalidGroupNameException;
use Regine\Exceptions\Group\NameForUnnamedGroupException;
use Regine\Exceptions\Group\NoNameForNamedGroupException;
use Regine\Regine;

/**
 * Group component
 *
 * Implements a regex component that represents a group
 * (capturing, non-capturing, named, atomic, conditional)
 */
class GroupComponent implements RegexComponent
{
    private GroupTypesEnum $type;
    private string $pattern;
    private ?string $name;
    private ?string $condition;
    private ?string $elsePattern;

    /**
     * Initializes a regex group component with the specified type, pattern, and optional parameters.
     *
     * @param  GroupTypesEnum  $type  The type of regex group (e.g., capturing, non-capturing, named, atomic, conditional).
     * @param  Regine|string  $pattern  The regex pattern contained within the group.
     * @param  string|null  $name  The name for named groups, if applicable.
     * @param  string|null  $condition  The condition for conditional groups, if applicable.
     * @param  Regine|string|null  $elsePattern  The pattern for the else branch in conditional groups, if applicable.
     *
     * @throws NoNameForNamedGroupException If the group is named but has no name.
     * @throws InvalidGroupNameException If the group name is invalid.
     * @throws ConditionalGroupWithNoConditionException If the group is conditional but has no condition.
     * @throws NameForUnnamedGroupException If the group is unnamed (numbered) but has a name.
     * @throws ConditionForUncoditionalGroupException If the group is not conditional but has a condition or else pattern.
     * @throws EmptyGroupPatternException If the group pattern is empty.
     */
    public function __construct(
        GroupTypesEnum $type,
        Regine|string $pattern,
        ?string $name = null,
        ?string $condition = null,
        Regine|string|null $elsePattern = null
    ) {
        $this->type = $type;
        $this->pattern = $this->compilePattern($pattern);
        $this->name = $name;
        $this->condition = $condition;
        $this->elsePattern = $elsePattern ? $this->compilePattern($elsePattern) : null;

        $this->validateParameters();
    }

    /**
     * Compiles the group component into its corresponding regex string representation based on the group type.
     *
     * @return string The compiled regex string for the group.
     */
    public function compile(): string
    {
        return match ($this->type) {
            GroupTypesEnum::CAPTURING => "({$this->pattern})",
            GroupTypesEnum::NON_CAPTURING => "(?:{$this->pattern})",
            GroupTypesEnum::NAMED => "(?<{$this->name}>{$this->pattern})",
            GroupTypesEnum::ATOMIC => "(?>{$this->pattern})",
            GroupTypesEnum::CONDITIONAL => $this->compileConditionalGroup(),
        };
    }

    /**
     * Returns the string identifier for this component type.
     *
     * @return string The string 'group'.
     */
    public function getType(): string
    {
        return 'group';
    }

    /**
     * Returns an associative array containing metadata about the group component, including its type, group type, pattern, name, condition, else pattern, and enum value.
     *
     * @return array<string, mixed> Associative array with keys: type, group_type, pattern, name, condition, else_pattern, and enum.
     */
    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'group_type' => $this->type->value,
            'pattern' => $this->pattern,
            'name' => $this->name,
            'condition' => $this->condition,
            'else_pattern' => $this->elsePattern,
            'enum' => $this->type->value,
        ];
    }

    /**
     * Indicates whether the group component can be quantified by quantifiers such as `*`, `+`, or `{n}`.
     *
     * @return bool Always returns true.
     */
    public function canBeQuantified(): bool
    {
        return true;
    }

    /**
     * Returns a human-readable description of the regex group, including its type, optional name, condition, and contained pattern.
     *
     * @return string The description of the group component.
     */
    public function getDescription(): string
    {
        $description = $this->type->getDescription();

        if ($this->name) {
            $description .= " named '{$this->name}'";
        }

        if ($this->condition) {
            $description .= " with condition '{$this->condition}'";
        }

        return $description . " containing '{$this->pattern}'";
    }

    /**
     * Returns the string representation of a pattern, compiling it if it is a Regine instance.
     *
     * @param  Regine|string  $pattern  The pattern to compile or return as a string.
     * @return string The compiled pattern string.
     */
    private function compilePattern(Regine|string $pattern): string
    {
        if ($pattern instanceof Regine) {
            // Use the raw compilation method for nesting patterns
            return $pattern->compileRaw();
        }

        return $pattern;
    }

    /**
     * Compiles and returns the regex string for a conditional group.
     *
     * Constructs a conditional group in the format `(?({condition}){pattern}|{elsePattern})`.
     * Throws a ConditionalGroupWithNoConditionException if the condition is not set.
     *
     * @return string The compiled regex string for the conditional group.
     *
     * @throws ConditionalGroupWithNoConditionException If the condition is not set.
     */
    private function compileConditionalGroup(): string
    {
        if (! $this->condition) {
            throw new ConditionalGroupWithNoConditionException;
        }

        $compiled = "(?({$this->condition}){$this->pattern}";

        if ($this->elsePattern) {
            $compiled .= "|{$this->elsePattern}";
        }

        return $compiled . ')';
    }

    /**
     * Validates the parameters for the group based on its type.
     *
     * Ensures that named groups have valid names, conditional groups have conditions, only appropriate group types have names or conditions, and that the pattern is not empty.
     *
     * @throws NoNameForNamedGroupException If the group is named but has no name.
     * @throws InvalidGroupNameException If the group name is invalid.
     * @throws ConditionalGroupWithNoConditionException If the group is conditional but has no condition.
     * @throws NameForUnnamedGroupException If the group is unnamed (numbered) but has a name.
     * @throws ConditionForUncoditionalGroupException If the group is not conditional but has a condition or else pattern.
     * @throws EmptyGroupPatternException If the group pattern is empty.
     */
    private function validateParameters(): void
    {
        if ($this->type === GroupTypesEnum::NAMED && ! $this->name) {
            throw new NoNameForNamedGroupException;
        }

        if ($this->type === GroupTypesEnum::NAMED && ! $this->isValidGroupName($this->name)) {
            throw new InvalidGroupNameException;
        }

        if ($this->type === GroupTypesEnum::CONDITIONAL && ! $this->condition) {
            throw new ConditionalGroupWithNoConditionException;
        }

        if ($this->type !== GroupTypesEnum::NAMED && $this->name) {
            throw new NameForUnnamedGroupException;
        }

        if ($this->type !== GroupTypesEnum::CONDITIONAL && ($this->condition || $this->elsePattern)) {
            throw new ConditionForUncoditionalGroupException;
        }

        if ($this->pattern === '') {
            throw new EmptyGroupPatternException;
        }
    }

    /**
     * Determines if the provided group name is valid according to regex group naming rules.
     *
     * A valid group name must start with a letter or underscore, followed by any combination of letters, digits, or underscores.
     *
     * @param  string|null  $name  The group name to validate.
     * @return bool True if the name is valid; otherwise, false.
     */
    private function isValidGroupName(?string $name): bool
    {
        if (! $name) {
            return false;
        }

        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name) === 1;
    }
}
