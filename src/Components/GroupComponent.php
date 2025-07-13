<?php

declare(strict_types=1);

namespace Regine\Components;

use InvalidArgumentException;
use Regine\Contracts\RegexComponent;
use Regine\Enums\GroupTypesEnum;
use Regine\Regine;

class GroupComponent implements RegexComponent
{
    private GroupTypesEnum $type;
    private string $pattern;
    private ?string $name;
    private ?string $condition;
    private ?string $elsePattern;

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

    public function getType(): string
    {
        return 'group';
    }

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

    public function canBeQuantified(): bool
    {
        return true;
    }

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

    private function compilePattern(Regine|string $pattern): string
    {
        if ($pattern instanceof Regine) {
            // Use the raw compilation method for nesting patterns
            return $pattern->compileRaw();
        }

        return $pattern;
    }

    private function compileConditionalGroup(): string
    {
        if (! $this->condition) {
            throw new InvalidArgumentException('Conditional group requires a condition.');
        }

        $compiled = "(?({$this->condition}){$this->pattern}";

        if ($this->elsePattern) {
            $compiled .= "|{$this->elsePattern}";
        }

        return $compiled . ')';
    }

    private function validateParameters(): void
    {
        if ($this->type === GroupTypesEnum::NAMED && ! $this->name) {
            throw new InvalidArgumentException('Named group requires a name.');
        }

        if ($this->type === GroupTypesEnum::NAMED && ! $this->isValidGroupName($this->name)) {
            throw new InvalidArgumentException('Invalid group name. Must contain only letters, digits, and underscores, and cannot start with a digit.');
        }

        if ($this->type === GroupTypesEnum::CONDITIONAL && ! $this->condition) {
            throw new InvalidArgumentException('Conditional group requires a condition.');
        }

        if ($this->type !== GroupTypesEnum::NAMED && $this->name) {
            throw new InvalidArgumentException('Only named groups can have a name.');
        }

        if ($this->type !== GroupTypesEnum::CONDITIONAL && ($this->condition || $this->elsePattern)) {
            throw new InvalidArgumentException('Only conditional groups can have conditions or else patterns.');
        }

        if ($this->pattern === '') {
            throw new InvalidArgumentException('Group pattern cannot be empty.');
        }
    }

    private function isValidGroupName(?string $name): bool
    {
        if (! $name) {
            return false;
        }

        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name) === 1;
    }
}
