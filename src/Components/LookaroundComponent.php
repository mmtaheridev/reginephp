<?php

declare(strict_types=1);

namespace Regine\Components;

use Regine\Contracts\RegexComponent;
use Regine\Enums\LookaroundTypesEnum;
use Regine\Regine;

class LookaroundComponent implements RegexComponent
{
    private LookaroundTypesEnum $type;
    private string $pattern;

    public function __construct(LookaroundTypesEnum $type, Regine|string $pattern)
    {
        $this->type = $type;
        $this->pattern = $this->compilePattern($pattern);
    }

    /**
     * Create a positive lookahead component
     * 
     * positive lookahead is a lookahead that asserts that the pattern exists before the current position
     * 
     * <code>
     * // Regular expression: `(?=pattern)`
     * 
     * $pattern = Regine::make()->positiveLookahead('test')->compile();
     * echo $pattern; // /(?=test)/
     * </code>
     */
    public static function positiveLookahead(Regine|string $pattern): self
    {
        return new self(LookaroundTypesEnum::POSITIVE_LOOKAHEAD, $pattern);
    }

    /**
     * Create a negative lookahead component
     * 
     * negative lookahead is a lookahead that asserts that the pattern does not exist before the current position
     * 
     * <code>
     * // Regular expression: `(?!pattern)`
     * 
     * $pattern = Regine::make()->negativeLookahead('test')->compile();
     * echo $pattern; // /(?!test)/
     * </code>
     */
    public static function negativeLookahead(Regine|string $pattern): self
    {
        return new self(LookaroundTypesEnum::NEGATIVE_LOOKAHEAD, $pattern);
    }

    /**
     * Create a positive lookbehind component
     * 
     * positive lookbehind is a lookbehind that asserts that the pattern exists after the current position
     * 
     * <code>
     * // Regular expression: `(?<=pattern)`
     * 
     * $pattern = Regine::make()->positiveLookbehind('test')->compile();
     * echo $pattern; // /(?<=test)/
     * </code>
     */
    public static function positiveLookbehind(Regine|string $pattern): self
    {
        return new self(LookaroundTypesEnum::POSITIVE_LOOKBEHIND, $pattern);
    }

    /**
     * Create a negative lookbehind component
     * 
     * negative lookbehind is a lookbehind that asserts that the pattern does not exist after the current position
     * 
     * <code>
     * // Regular expression: `(?<!pattern)`
     * 
     * $pattern = Regine::make()->negativeLookbehind('test')->compile();
     * echo $pattern; // /(?<!test)/
     * </code>
     */
    public static function negativeLookbehind(Regine|string $pattern): self
    {
        return new self(LookaroundTypesEnum::NEGATIVE_LOOKBEHIND, $pattern);
    }

    /**
     * Compile the lookaround component into a regex string
     */
    public function compile(): string
    {
        return $this->type->getPattern($this->pattern);
    }

    /**
     * Get the type of the lookaround component
     */
    public function getType(): string
    {
        return 'lookaround';
    }

    /**
     * Get metadata about the lookaround component
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return [
            'type' => $this->getType(),
            'lookaround_type' => $this->type->value,
            'pattern' => $this->pattern,
            'enum' => $this->type->name,
        ];
    }

    /**
     * Check if the lookaround component can be quantified
     */
    public function canBeQuantified(): bool
    {
        return false; // Lookarounds cannot be quantified
    }

    /**
     * Get a human-readable description of the lookaround component
     */
    public function getDescription(): string
    {
        return "{$this->type->getDescription()} assertion for '{$this->pattern}'";
    }

    /**
     * Compile a pattern (Regine object or string) to its string representation
     */
    private function compilePattern(Regine|string $pattern): string
    {
        if ($pattern instanceof Regine) {
            // Use the raw compilation method for nesting patterns
            return $pattern->compileRaw();
        }

        return $pattern;
    }
} 