<?php

declare(strict_types=1);

namespace Na\MockCheck\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Node\Expr>
 */
final class ForbiddenMockRule implements Rule
{
    private const MOCK_METHODS = [
        'createMock',
        'createStub',
        'createPartialMock',
        'createConfiguredMock',
        'createTestProxy',
        'getMockBuilder',
        'getMock',
        'getMockForAbstractClass',
        'getMockForTrait',
    ];

    /** @var list<array{pattern: string, message: string|null}> */
    private array $forbiddenPatterns;

    /**
     * @param list<array{pattern: string, message?: string}> $forbiddenPatterns
     */
    public function __construct(array $forbiddenPatterns = [])
    {
        $this->forbiddenPatterns = array_map(
            static fn(array $config): array => [
                'pattern' => $config['pattern'],
                'message' => $config['message'] ?? null,
            ],
            $forbiddenPatterns,
        );
    }

    public function getNodeType(): string
    {
        return Node\Expr::class;
    }

    /**
     * @param Node\Expr $node
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof MethodCall && !$node instanceof StaticCall) {
            return [];
        }

        if (!$node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();

        if (!in_array($methodName, self::MOCK_METHODS, true)) {
            return [];
        }

        $args = $node->getArgs();
        if ($args === []) {
            return [];
        }

        $firstArg = $args[0]->value;
        $mockedClass = $this->resolveMockedClass($firstArg, $scope);

        if ($mockedClass === null) {
            return [];
        }

        foreach ($this->forbiddenPatterns as $config) {
            if ($this->matchesPattern($mockedClass, $config['pattern'])) {
                $message = $config['message'] ?? sprintf(
                    'Mocking %s is forbidden (matches pattern "%s").',
                    $mockedClass,
                    $config['pattern'],
                );

                return [
                    RuleErrorBuilder::message($message)
                        ->identifier('norman.mockCheck.forbiddenMock')
                        ->build(),
                ];
            }
        }

        return [];
    }

    private function resolveMockedClass(Node\Expr $expr, Scope $scope): ?string
    {
        if ($expr instanceof Node\Expr\ClassConstFetch
            && $expr->class instanceof Node\Name
            && $expr->name instanceof Identifier
            && $expr->name->toString() === 'class'
        ) {
            return $scope->resolveName($expr->class);
        }

        $type = $scope->getType($expr);
        $constantStrings = $type->getConstantStrings();
        if ($constantStrings !== []) {
            return $constantStrings[0]->getValue();
        }

        return null;
    }

    private function matchesPattern(string $className, string $pattern): bool
    {
        if ($className === $pattern) {
            return true;
        }

        if (str_ends_with($pattern, '\\')) {
            return str_starts_with($className, $pattern);
        }

        if (str_contains($pattern, '*')) {
            $regex = '/^' . str_replace('\\*', '.*', preg_quote($pattern, '/')) . '$/';
            return (bool) preg_match($regex, $className);
        }

        return str_starts_with($className, $pattern . '\\');
    }
}
