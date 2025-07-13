<?php

declare(strict_types=1);

namespace Regine\Composables;

use InvalidArgumentException;
use Regine\Collections\PatternCollection;
use Regine\Components\GroupComponent;
use Regine\Components\QuantifierComponent;
use Regine\Enums\GroupTypesEnum;
use RuntimeException;

trait HasQuantifiers
{
    /**
     * Match zero or more occurrences of the preceding element
     */
    public function zeroOrMore(): self
    {
        $this->addQuantifier(QuantifierComponent::zeroOrMore());

        return $this;
    }

    /**
     * Match one or more occurrences of the preceding element
     */
    public function oneOrMore(): self
    {
        $this->addQuantifier(QuantifierComponent::oneOrMore());

        return $this;
    }

    /**
     * Match zero or one occurrence of the preceding element
     */
    public function optional(): self
    {
        $this->addQuantifier(QuantifierComponent::optional());

        return $this;
    }

    /**
     * Match exactly n occurrences of the preceding element
     */
    public function exactly(int $n): self
    {
        $this->addQuantifier(QuantifierComponent::exactly($n));

        return $this;
    }

    /**
     * Match at least n occurrences of the preceding element
     */
    public function atLeast(int $n): self
    {
        $this->addQuantifier(QuantifierComponent::atLeast($n));

        return $this;
    }

    /**
     * Match between min and max occurrences of the preceding element
     */
    public function between(int $min, int $max): self
    {
        $this->addQuantifier(QuantifierComponent::between($min, $max));

        return $this;
    }

    /**
     * Add a quantifier to the last component
     */
    private function addQuantifier(QuantifierComponent $quantifier): void
    {
        $lastComponent = $this->components->getLastComponent();

        if ($lastComponent === null) {
            throw new InvalidArgumentException('Cannot add quantifier: no preceding element.');
        }

        if (! $lastComponent->canBeQuantified()) {
            throw new InvalidArgumentException('Cannot quantify the preceding element.');
        }

        // If the last component is an alternation, wrap it in a non-capturing group
        if ($lastComponent->getType() === 'alternation') {
            // Remove the alternation component and wrap it in a group
            $components = $this->components->getComponents();
            $alternationComponent = array_pop($components);

            if ($alternationComponent === null) {
                throw new RuntimeException('Expected alternation component but found null');
            }

            // Create a new pattern collection without the alternation
            $this->components = new PatternCollection;
            foreach ($components as $component) {
                $this->components->add($component);
            }

            // Wrap the alternation in a non-capturing group
            $groupComponent = new GroupComponent(
                GroupTypesEnum::NON_CAPTURING,
                $alternationComponent->compile()
            );

            $this->components->add($groupComponent);

            // Update the last component reference
            $lastComponent = $groupComponent;
        }

        $this->components->add($quantifier);
    }
}
