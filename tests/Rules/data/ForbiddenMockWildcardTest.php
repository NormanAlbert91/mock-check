<?php

declare(strict_types=1);

namespace Na\MockCheck\Tests\Rules\Data;

use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

final class ForbiddenMockWildcardTest extends TestCase
{
    public function testSomething(): void
    {
        $user = $this->createMock(User::class);
    }
}
