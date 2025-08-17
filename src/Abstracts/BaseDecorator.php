<?php

declare(strict_types=1);

namespace Regine\Abstracts;

use Regine\Contracts\RegexDecorator;
use Regine\Contracts\RegexElement;
use Regine\Enums\DecoratorType;

/**
 * Abstract base class for all regex decorators.
 *
 * Provides common functionality for decorator elements that wrap
 * or modify other regex elements.
 */
abstract class BaseDecorator implements RegexDecorator
{
    protected RegexElement $element;
    protected DecoratorType $decoratorType;

    public function __construct(RegexElement $element)
    {
        $this->element = $element;
        $this->decoratorType = $this->getDecoratorType();
    }

    public function getWrappedElement(): RegexElement
    {
        return $this->element;
    }

    public function canBeDecorated(): bool
    {
        return true; // Most decorators can be decorated by other decorators
    }

    public function getPrecedence(): int
    {
        return $this->decoratorType->getPrecedence();
    }

    public function canBeQuantified(): bool
    {
        // Decorators can generally be quantified unless they're already quantifiers
        return $this->decoratorType !== DecoratorType::QUANTIFIER;
    }

    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'decorator_type' => $this->decoratorType->value,
            'precedence' => $this->getPrecedence(),
            'wrapped_element' => $this->element->getMetadata(),
            'can_be_decorated' => $this->canBeDecorated(),
            'can_be_quantified' => $this->canBeQuantified(),
            'needs_grouping' => $this->needsGrouping(),
        ];
    }

    public function getDescription(): string
    {
        $decoratorDesc = $this->getDecoratorDescription();
        $elementDesc = $this->element->getDescription();

        return "{$decoratorDesc} applied to ({$elementDesc})";
    }

    /**
     * Get the specific decorator type for this decorator.
     */
    abstract protected function getDecoratorType(): DecoratorType;

    /**
     * Get a description of what this specific decorator does.
     */
    abstract protected function getDecoratorDescription(): string;

    /**
     * Determines if the wrapped element needs to be grouped for proper compilation.
     * This is usually true for complex elements that contain multiple parts.
     */
    protected function wrappedElementNeedsGrouping(): bool
    {
        // Check if the wrapped element is a decorator that indicates grouping needs
        if ($this->element instanceof RegexDecorator) {
            return $this->element->needsGrouping();
        }

        // Check element type and complexity
        $elementType = $this->element->getType();
        // FIXME : composite type is not handled yet and is not in the codebase
        // should be handled and added to the codebase
        // TODO: then the string types should come through enum values
        $complexTypes = ['alternation', 'composite'];

        return in_array($elementType, $complexTypes, true);
    }
}
