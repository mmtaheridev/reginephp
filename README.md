# Regine

<div align="center">
  <img src="assets/regine-logo.png" alt="Regine Logo" width="300">
  
  **An Expressive PHP Library for Crafting Regular Expressions fluently.**
  
  [![Latest Version](https://img.shields.io/packagist/v/mmtaheridev/reginephp.svg?style=flat-square)](https://packagist.org/packages/mmtaheridev/reginephp)
  [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
  [![PHP Version](https://img.shields.io/badge/php-%5E8.3-787CB5.svg?style=flat-square)](https://php.net)
  [![Tests](https://img.shields.io/badge/tests-passing-brightgreen.svg?style=flat-square)](https://github.com/mmtaheridev/reginephp)
</div>

---

## 🚀 The Problem Regine Solves

Regular expressions are powerful but **notoriously difficult** to read, write, and maintain. A simple email validation can look like this:

```regex
/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
```

**Can you immediately understand what this does?** Probably not. And if you need to modify it in 6 months, good luck!

Regine transforms this cryptic pattern into **readable, fluent PHP code**:

```php
use Regine\Regine;

$emailPattern = Regine::make()
    ->startOfString()
    ->anyOf('a-zA-Z0-9._%+-')->oneOrMore()
    ->literal('@')
    ->anyOf('a-zA-Z0-9.-')->oneOrMore()
    ->literal('.')
    ->anyOf('a-zA-Z')->between(2, 4)
    ->endOfString()
    ->compile();
```

**Now it reads like English!** 🎉

## 🏗️ Architecture: Component-Decorator Pattern

Regine uses a clean **Component-Decorator architecture** that separates concerns:

- **🧩 Components**: Actual regex content (literals, character classes, anchors, etc.)
- **🎨 Decorators**: Elements that modify/wrap components (groups, quantifiers, lookarounds)

This design provides:
- ✅ **Clean separation of concerns**
- ✅ **Simplified grouping logic**
- ✅ **Maintainable and extensible codebase**
- ✅ **Intuitive API design**

## 📦 Installation

Install Regine via Composer:

```bash
composer require mmtaheridev/reginephp
```

**Requirements:**
- PHP 8.3 or higher
- ext-mbstring (for Unicode support)

## 🎯 Quick Start

### Basic Usage

```php
<?php

require 'vendor/autoload.php';

use Regine\Regine;

// Create a simple pattern
$pattern = Regine::make()
    ->literal('Hello')
    ->whitespace()
    ->literal('World')
    ->compile();

echo $pattern; // Output: /Hello\sWorld/

// Test the pattern
$regine = Regine::make()->literal('Hello')->whitespace()->literal('World');
var_dump($regine->test('Hello World')); // bool(true)
```

### Character Matching

```php
// Match digits
$digitPattern = Regine::make()
    ->startOfString()
    ->digit()->oneOrMore()
    ->endOfString()
    ->compile();
// Output: /^\d+$/

// Match word characters  
$wordPattern = Regine::make()
    ->wordChar()->zeroOrMore()
    ->compile();
// Output: /\w*/

// Match specific characters
$vowelPattern = Regine::make()
    ->anyOf('aeiou')->oneOrMore()
    ->compile();
// Output: /[aeiou]+/

// Advanced character classes
$letterPattern = Regine::make()
    ->letter()->oneOrMore()          // [a-zA-Z]+
    ->compile();

$hexPattern = Regine::make()
    ->range('0', '9')                // [0-9]
    ->range('a', 'f')                // [a-f] 
    ->range('A', 'F')                // [A-F]
    ->oneOrMore()
    ->compile();
```

### Quantifiers

```php
// Various quantifiers
$pattern = Regine::make()
    ->literal('a')->optional()          // a?
    ->literal('b')->zeroOrMore()        // b*
    ->literal('c')->oneOrMore()         // c+
    ->literal('d')->exactly(3)          // d{3}
    ->literal('e')->atLeast(2)          // e{2,}
    ->literal('f')->between(1, 5)       // f{1,5}
    ->compile();
// Output: /a?b*c+d{3}e{2,}f{1,5}/

// Quantifiers with shorthand characters
$complexPattern = Regine::make()
    ->digit()->oneOrMore()              // \d+
    ->whitespace()->zeroOrMore()        // \s*
    ->wordChar()->between(3, 10)        // \w{3,10}
    ->compile();
// Output: /\d+\s*\w{3,10}/
```

### Character Classes and Ranges

```php
// Character classes
$pattern = Regine::make()
    ->anyOf('abc')              // [abc]
    ->noneOf('xyz')             // [^xyz]
    ->range('a', 'z')           // [a-z]
    ->range('A', 'Z')           // [A-Z]
    ->range('0', '9')           // [0-9]
    ->compile();
// Output: /[abc][^xyz][a-z][A-Z][0-9]/

// Advanced character classes
$unicodePattern = Regine::make()
    ->anyOf('αβγ')              // Unicode characters [αβγ]
    ->range('α', 'ω')           // Unicode range [α-ω]
    ->compile();
// Output: /[αβγ][α-ω]/u (note automatic Unicode flag)

// Negated ranges
$notDigitsPattern = Regine::make()
    ->noneOfRange('0', '9')     // [^0-9]
    ->oneOrMore()
    ->compile();
// Output: /[^0-9]+/
```

### Groups and Alternation

```php
// Groups
$pattern = Regine::make()
    ->group(
        Regine::make()->literal('cat')->or('dog')
    )
    ->compile(); // Output: /(cat|dog)/

// Named groups
$pattern = Regine::make()
    ->namedGroup('animal',
        Regine::make()->literal('cat')->or('dog')
    )
    ->compile(); // Output: /(?<animal>cat|dog)/

// Simple alternation
$pattern = Regine::make()
    ->oneOf(['cat', 'dog', 'bird'])
    ->compile(); // Output: /cat|dog|bird/
```

### Advanced Features

```php
// Lookarounds
$lookaheadPattern = Regine::make()
    ->literal('test')
    ->lookahead('ing')                   // (?=ing)
    ->compile();
// Output: /test(?=ing)/

$passwordPattern = Regine::make()
    ->startOfString()
    ->lookahead('.*[a-z]')              // Must contain lowercase
    ->lookahead('.*[A-Z]')              // Must contain uppercase  
    ->lookahead('.*\d')                 // Must contain digit
    ->anyChar()->atLeast(8)             // At least 8 characters
    ->endOfString()
    ->compile();
// Output: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/

// Anchors and boundaries
$wordPattern = Regine::make()
    ->startOfString()
    ->wordBoundary()
    ->literal('hello')
    ->wordBoundary()
    ->endOfString()
    ->compile();
// Output: /^\bhello\b$/

// Flags with method chaining
$flagPattern = Regine::make()
    ->literal('hello')
    ->caseInsensitive()     // i flag
    ->multiline()           // m flag
    ->dotAll()              // s flag
    ->compile();
// Output: /hello/ims

// Short flag methods
$shortFlagPattern = Regine::make()
    ->literal('test')
    ->i()                   // case insensitive
    ->m()                   // multiline
    ->s()                   // dot all
    ->u()                   // unicode
    ->compile();
// Output: /test/imsu
```

## 🔍 Debugging and Testing

Regine provides excellent debugging capabilities:

```php
$regine = Regine::make()
    ->startOfString()
    ->oneOrMore()->digit()
    ->endOfString();

// Get debug information
$debug = $regine->debug();
print_r($debug->toArray());

// Test patterns
var_dump($regine->test('12345'));     // bool(true)
var_dump($regine->test('abc'));       // bool(false)

// Get matches
$matches = $regine->matches('12345');
print_r($matches);

// Get human-readable description
echo $regine->describe();

// Check if pattern is empty
var_dump($regine->isEmpty());         // bool(false)

// Get element count
echo $regine->getElementCount();      // int
```

## 🎨 Real-World Examples

### Email Validation

```php
$emailPattern = Regine::make()
    ->startOfString()
    ->wordChar()->oneOrMore()
    ->anyOf('.-_')->optional()
    ->wordChar()->zeroOrMore()
    ->literal('@')
    ->wordChar()->oneOrMore()
    ->literal('.')
    ->wordChar()->between(2, 4)
    ->endOfString();

// Advanced email validation with lookaheads
$strictEmailPattern = Regine::make()
    ->startOfString()
    ->lookahead('.*@')                  // Must contain @
    ->lookahead('.*\.')                 // Must contain .
    ->negativeLookahead('.*@.*@')       // No double @
    ->wordChar()->oneOrMore()
    ->literal('@')
    ->wordChar()->oneOrMore()
    ->literal('.')
    ->wordChar()->between(2, 4)
    ->endOfString();

// Test both patterns
var_dump($emailPattern->test('user@example.com'));      // true
var_dump($emailPattern->test('invalid.email'));         // false
var_dump($strictEmailPattern->test('user@@example.com')); // false
```

### Phone Number Validation

```php
$phonePattern = Regine::make()
    ->startOfString()
    ->literal('(')->optional()
    ->digit()->exactly(3)
    ->literal(')')->optional()
    ->anyOf(' -')->optional()
    ->digit()->exactly(3)
    ->anyOf(' -')
    ->digit()->exactly(4)
    ->endOfString();

// Alternative using groups for better organization
$groupedPhonePattern = Regine::make()
    ->startOfString()
    ->group(
        Regine::make()
            ->literal('(')
            ->digit()->exactly(3)
            ->literal(')')
    )->optional()
    ->whitespace()->optional()
    ->digit()->exactly(3)
    ->anyOf(' -.')
    ->digit()->exactly(4)
    ->endOfString();

// Test various formats
var_dump($phonePattern->test('(555) 123-4567')); // true
var_dump($phonePattern->test('555-123-4567'));   // true
var_dump($phonePattern->test('5551234567'));     // false (needs separators)
```

### URL Validation

```php
$urlPattern = Regine::make()
    ->startOfString()
    ->group(
        Regine::make()->oneOf(['http', 'https'])
    )
    ->literal('://')
    ->anyOf('a-zA-Z0-9.-')->oneOrMore()
    ->literal('.')
    ->anyOf('a-zA-Z')->between(2, 4)
    ->anyOf('/a-zA-Z0-9._~:/?#[]@!$&\'()*+,;=-')->zeroOrMore()
    ->endOfString();

// More flexible URL pattern with optional parts
$flexibleUrlPattern = Regine::make()
    ->startOfString()
    ->group(
        Regine::make()->oneOf(['http', 'https', 'ftp'])
    )
    ->literal('://')
    ->group(                            // Optional auth
        Regine::make()
            ->wordChar()->oneOrMore()
            ->literal(':')
            ->wordChar()->oneOrMore()
            ->literal('@')
    )->optional()
    ->anyOf('a-zA-Z0-9.-')->oneOrMore() // Domain
    ->group(                            // Optional port
        Regine::make()
            ->literal(':')
            ->range('0', '9')->between(1, 5)
    )->optional()
    ->group(                            // Optional path
        Regine::make()
            ->literal('/')
            ->anyOf('a-zA-Z0-9._~:/?#[]@!$&\'()*+,;=-')->zeroOrMore()
    )->optional()
    ->endOfString();

var_dump($urlPattern->test('https://example.com/path')); // true
var_dump($flexibleUrlPattern->test('https://user:pass@example.com:8080/path')); // true
```

## 🧪 Testing

Regine comes with comprehensive test coverage:

```bash
# Run tests
./vendor/bin/pest

# Run tests with coverage
./vendor/bin/pest --coverage

# Run static analysis
./vendor/bin/phpstan analyse

# Format code
./vendor/bin/duster fix
```

## 🌟 Future Roadmap

We're building something **revolutionary** for the PHP ecosystem. Here's what's coming:

### 🎯 Comprehensive Pattern Library
- **Hundreds of Pre-built Patterns**: Email, phone, URL, credit card, social security, and much more
- **Zero-Regex Philosophy**: Handle 99% of use cases without writing raw regex
- **Domain-Specific Patterns**: Financial, geographic, technical, and specialized patterns

### 🚀 Enterprise Features
- **Pattern Composition Tools**: Combine and extend patterns effortlessly
- **Performance Optimizations**: Sub-millisecond compilation for complex patterns
- **Advanced Debugging**: Visual pattern representation and analysis tools
- **International Support**: Localized patterns for global applications

### 🔧 Developer Experience
- **IDE Integration**: Full autocompletion and documentation
- **Pattern Validation**: Real-time pattern testing and validation
- **Framework Integration**: Laravel, Symfony, and other framework integrations

*Stay tuned - we're just getting started! 🚀*

## 🤝 Contributing

We welcome contributions!.

### Development Setup

```bash
git clone https://github.com/mmtaheridev/reginephp.git
cd reginephp
composer install
./vendor/bin/pest
```

## 📄 License

Regine is open-sourced software licensed under the [MIT license](LICENSE.md).

## 🙏 Acknowledgments

- Inspired by Laravel's Eloquent ORM fluent interface design
- Built with PHP 8.3+ features for modern development
- Tested with Pest PHP for reliable quality assurance

---

<div align="center">
  <strong>Made with ❤️ by <a href="https://github.com/mmtaheridev">Mohammad Mahdi Taheri</a></strong>
  
  ⭐ **Star this repo if you find it useful!** ⭐
</div>
