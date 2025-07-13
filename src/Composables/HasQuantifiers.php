<?php

declare(strict_types=1);

namespace Regine\Composables;

use InvalidArgumentException;
use Regine\Components\QuantifierComponent;

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

        $this->components->add($quantifier);
    }
}
