<?php

declare(strict_types=1);

namespace Na\MockCheck\Tests\Rules\Data;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ForbiddenMockPsrTest extends TestCase
{
    public function testSomething(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
    }
}
