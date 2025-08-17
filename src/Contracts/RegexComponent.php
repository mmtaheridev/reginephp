<?php

declare(strict_types=1);

namespace Regine\Contracts;

/**
 * Interface for regex components that represent actual content.
 *
 * Components are elements that generate regex content (literals, character classes,
 * anchors, etc.) as opposed to decorators that modify or wrap other elements.
 */
interface RegexComponent extends RegexElement
{
    // All methods inherited from RegexElement
    // Components focus on generating regex content
}
