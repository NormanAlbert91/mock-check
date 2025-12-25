<?php

declare(strict_types=1);

namespace Na\MockCheck\Tests\Rules\Data;

use App\Contracts\PaymentGatewayInterface;
use PHPUnit\Framework\TestCase;

final class ForbiddenMockExactTest extends TestCase
{
    public function testSomething(): void
    {
        $gateway = $this->createMock(PaymentGatewayInterface::class);
    }
}
