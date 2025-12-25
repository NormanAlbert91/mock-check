<?php

declare(strict_types=1);

namespace Na\MockCheck\Tests\Rules\Data;

use App\Service\MyServiceInterface;
use PHPUnit\Framework\TestCase;

final class AllowedMockTest extends TestCase
{
    public function testSomething(): void
    {
        $service = $this->createMock(MyServiceInterface::class);
    }
}
