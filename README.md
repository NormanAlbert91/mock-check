# PHPStan Mock Check

A PHPStan plugin to forbid mocking specific interfaces or classes.

## Installation

```bash
composer require --dev na/mock-check
```

If you use [phpstan/extension-installer](https://github.com/phpstan/extension-installer), the plugin is automatically registered. Otherwise, add to your `phpstan.neon`:

```neon
includes:
    - vendor/na/mock-check/extension.neon
```

## Configuration

Add forbidden patterns to your `phpstan.neon`:

```neon
parameters:
    mockCheck:
        forbiddenPatterns:
            -
                pattern: 'Psr\'
                message: 'PSR interfaces should not be mocked - use real implementations.'
            -
                pattern: 'Doctrine\ORM\EntityManagerInterface'
                message: 'Use the in-memory repository instead of mocking EntityManager.'
            -
                pattern: 'App\Domain\*'
```

### Pattern formats

| Pattern | Description |
|---------|-------------|
| `Psr\` | Trailing backslash: matches everything starting with `Psr\` |
| `Psr\Log` | Without backslash: matches everything under `Psr\Log\` |
| `Psr\Log\*` | Wildcard: regex-style matching |
| `Psr\Log\LoggerInterface` | Exact match |

### Custom messages

The `message` parameter is optional. If omitted, a default message is shown:

> Mocking Psr\Log\LoggerInterface is forbidden (matches pattern "Psr\").

## Detected mock methods

The plugin detects the following PHPUnit mock methods:

- `createMock()`
- `createStub()`
- `createPartialMock()`
- `createConfiguredMock()`
- `createTestProxy()`
- `getMockBuilder()`
- `getMock()`
- `getMockForAbstractClass()`
- `getMockForTrait()`
