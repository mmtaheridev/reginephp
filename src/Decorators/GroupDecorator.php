<?php

declare(strict_types=1);

namespace Regine\Decorators;

use Regine\Abstracts\BaseDecorator;
use Regine\Contracts\RegexElement;
use Regine\Enums\DecoratorType;
use Regine\Enums\GroupTypesEnum;
use Regine\Exceptions\Group\ConditionalGroupWithNoConditionException;
use Regine\Exceptions\Group\ConditionForUncoditionalGroupException;
use Regine\Exceptions\Group\InvalidGroupNameException;
use Regine\Exceptions\Group\NameForUnnamedGroupException;
use Regine\Exceptions\Group\NoNameForNamedGroupException;

/**
 * Decorator for wrapping elements in regex groups.
 *
 * Groups can be capturing, non-capturing, named, atomic, or conditional.
 * This decorator handles all group types and their specific requirements.
 */
class GroupDecorator extends BaseDecorator
{
    private GroupTypesEnum $groupType;
    private ?string $name;
    private ?string $condition;
    private ?RegexElement $elseBranch;

    public function __construct(
        RegexElement $element,
        GroupTypesEnum $groupType = GroupTypesEnum::CAPTURING,
        ?string $name = null,
        ?string $condition = null,
        ?RegexElement $elseBranch = null
    ) {
        parent::__construct($element);
        $this->groupType = $groupType;
        $this->name = $name;
        $this->condition = $condition;
        $this->elseBranch = $elseBranch;

        $this->validateParameters();
    }

    public function compile(): string
    {
        $content = $this->element->compile();

        return match ($this->groupType) {
            GroupTypesEnum::CAPTURING => "({$content})",
            GroupTypesEnum::NON_CAPTURING => "(?:{$content})",
            GroupTypesEnum::NAMED => "(?<{$this->name}>{$content})",
            GroupTypesEnum::ATOMIC => "(?>{$content})",
            GroupTypesEnum::CONDITIONAL => $this->compileConditionalGroup($content),
        };
    }

    public function getType(): string
    {
        return DecoratorType::GROUP->value;
    }

    public function needsGrouping(): bool
    {
        // Groups don't need additional grouping - they are already grouped
        return false;
    }

    public function getMetadata(): array
    {
        $metadata = parent::getMetadata();
        $metadata['group_type'] = $this->groupType->value;
        $metadata['group_description'] = $this->groupType->getDescription();

        if ($this->name !== null) {
            $metadata['group_name'] = $this->name;
        }

        if ($this->condition !== null) {
            $metadata['condition'] = $this->condition;
        }

        if ($this->elseBranch !== null) {
            $metadata['else_branch'] = $this->elseBranch->getMetadata();
        }

        return $metadata;
    }

    /**
     * Get the group type for this group decorator.
     */
    public function getGroupType(): GroupTypesEnum
    {
        return $this->groupType;
    }

    /**
     * Get the group name (for named groups).
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the condition (for conditional groups).
     */
    public function getCondition(): ?string
    {
        return $this->condition;
    }

    /**
     * Get the else branch (for conditional groups).
     */
    public function getElseBranch(): ?RegexElement
    {
        return $this->elseBranch;
    }

    protected function getDecoratorType(): DecoratorType
    {
        return DecoratorType::GROUP;
    }

    protected function getDecoratorDescription(): string
    {
        $description = $this->groupType->getDescription();

        if ($this->name !== null) {
            $description .= " '{$this->name}'";
        }

        if ($this->condition !== null) {
            $description .= " with condition '{$this->condition}'";
        }

        return $description;
    }

    private function validateParameters(): void
    {
        // Validate named group requirements
        if ($this->groupType === GroupTypesEnum::NAMED) {
            if ($this->name === null) {
                throw new NoNameForNamedGroupException;
            }
            $this->validateGroupName($this->name);
        } else {
            if ($this->name !== null) {
                throw new NameForUnnamedGroupException;
            }
        }

        // Validate conditional group requirements
        if ($this->groupType === GroupTypesEnum::CONDITIONAL) {
            if ($this->condition === null) {
                throw new ConditionalGroupWithNoConditionException;
            }
        } else {
            if ($this->condition !== null) {
                throw new ConditionForUncoditionalGroupException;
            }
        }
    }

    private function validateGroupName(string $name): void
    {
        // Group names must be valid identifiers (letters, digits, underscores)
        // Must start with a letter or underscore
        if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
            throw new InvalidGroupNameException($name);
        }
    }

    private function compileConditionalGroup(string $content): string
    {
        $condition = $this->condition;
        $elseContent = $this->elseBranch?->compile() ?? '';

        if ($elseContent !== '') {
            return "(?({$condition}){$content}|{$elseContent})";
        }

        return "(?({$condition}){$content})";
    }
}
