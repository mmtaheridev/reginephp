<?php

declare(strict_types=1);

namespace Regine\Decorators;

use Regine\Abstracts\BaseDecorator;
use Regine\Contracts\RegexElement;
use Regine\Enums\DecoratorType;
use Regine\Enums\LookaroundTypesEnum;

/**
 * Decorator for applying lookaround assertions to regex elements.
 *
 * Handles positive and negative lookahead and lookbehind assertions.
 * The decorated element becomes the assertion content, while the
 * primary pattern continues after the lookaround.
 */
class LookaroundDecorator extends BaseDecorator
{
    private LookaroundTypesEnum $lookaroundType;
    private RegexElement $assertionContent;

    public function __construct(
        RegexElement $element,
        LookaroundTypesEnum $lookaroundType,
        RegexElement $assertionContent
    ) {
        parent::__construct($element);
        $this->lookaroundType = $lookaroundType;
        $this->assertionContent = $assertionContent;
    }

    public function compile(): string
    {
        $assertionContent = $this->assertionContent->compile();

        // Generate the lookaround assertion
        $assertion = $this->lookaroundType->getPattern($assertionContent);

        // Lookarounds are zero-width assertions - they don't consume characters
        // So we only return the assertion itself
        return $assertion;
    }

    public function getType(): string
    {
        return DecoratorType::LOOKAROUND->value;
    }

    public function needsGrouping(): bool
    {
        // Lookaround decorators don't need additional grouping
        // They handle their own grouping via the assertion syntax
        return false;
    }

    public function getMetadata(): array
    {
        $metadata = parent::getMetadata();
        $metadata['lookaround_type'] = $this->lookaroundType->value;
        $metadata['lookaround_description'] = $this->lookaroundType->getDescription();
        $metadata['assertion_content'] = $this->assertionContent->getMetadata();
        $metadata['is_lookahead'] = $this->isLookahead();
        $metadata['is_lookbehind'] = $this->isLookbehind();
        $metadata['is_positive'] = $this->isPositive();
        $metadata['is_negative'] = $this->isNegative();

        return $metadata;
    }

    /**
     * Get the lookaround type.
     */
    public function getLookaroundType(): LookaroundTypesEnum
    {
        return $this->lookaroundType;
    }

    /**
     * Get the assertion content.
     */
    public function getAssertionContent(): RegexElement
    {
        return $this->assertionContent;
    }

    /**
     * Check if this is a lookahead assertion.
     */
    public function isLookahead(): bool
    {
        return in_array($this->lookaroundType, [
            LookaroundTypesEnum::POSITIVE_LOOKAHEAD,
            LookaroundTypesEnum::NEGATIVE_LOOKAHEAD,
        ], true);
    }

    /**
     * Check if this is a lookbehind assertion.
     */
    public function isLookbehind(): bool
    {
        return in_array($this->lookaroundType, [
            LookaroundTypesEnum::POSITIVE_LOOKBEHIND,
            LookaroundTypesEnum::NEGATIVE_LOOKBEHIND,
        ], true);
    }

    /**
     * Check if this is a positive assertion.
     */
    public function isPositive(): bool
    {
        return in_array($this->lookaroundType, [
            LookaroundTypesEnum::POSITIVE_LOOKAHEAD,
            LookaroundTypesEnum::POSITIVE_LOOKBEHIND,
        ], true);
    }

    /**
     * Check if this is a negative assertion.
     */
    public function isNegative(): bool
    {
        return in_array($this->lookaroundType, [
            LookaroundTypesEnum::NEGATIVE_LOOKAHEAD,
            LookaroundTypesEnum::NEGATIVE_LOOKBEHIND,
        ], true);
    }

    protected function getDecoratorType(): DecoratorType
    {
        return DecoratorType::LOOKAROUND;
    }

    protected function getDecoratorDescription(): string
    {
        $description = $this->lookaroundType->getDescription();
        $assertionDesc = $this->assertionContent->getDescription();

        return "{$description} assertion for '{$assertionDesc}'";
    }
}
