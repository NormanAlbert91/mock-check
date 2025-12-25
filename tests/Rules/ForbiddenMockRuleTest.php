<?php

declare(strict_types=1);

namespace Na\MockCheck\Tests\Rules;

use Na\MockCheck\Rules\ForbiddenMockRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Contracts\PaymentGatewayInterface;

/**
 * @extends RuleTestCase<ForbiddenMockRule>
 */
final class ForbiddenMockRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ForbiddenMockRule([
            ['pattern' => 'Psr\\', 'message' => 'PSR interfaces should not be mocked.'],
            ['pattern' => 'App\\Domain\\*'],
            ['pattern' => PaymentGatewayInterface::class],
        ]);
    }

    #[Test]
    public function detectsForbiddenPsrMockWithCustomMessage(): void
    {
        $this->analyse([__DIR__ . '/data/ForbiddenMockPsrTest.php'], [
            [
                'PSR interfaces should not be mocked.',
                14,
            ],
        ]);
    }

    #[Test]
    public function detectsForbiddenWildcardMockWithDefaultMessage(): void
    {
        $this->analyse([__DIR__ . '/data/ForbiddenMockWildcardTest.php'], [
            [
                'Mocking App\Domain\Entity\User is forbidden (matches pattern "App\Domain\*").',
                14,
            ],
        ]);
    }

    #[Test]
    public function detectsForbiddenExactMock(): void
    {
        $this->analyse([__DIR__ . '/data/ForbiddenMockExactTest.php'], [
            [
                'Mocking App\Contracts\PaymentGatewayInterface is forbidden (matches pattern "App\Contracts\PaymentGatewayInterface").',
                14,
            ],
        ]);
    }

    #[Test]
    public function allowsNonForbiddenMocks(): void
    {
        $this->analyse([__DIR__ . '/data/AllowedMockTest.php'], []);
    }
}
