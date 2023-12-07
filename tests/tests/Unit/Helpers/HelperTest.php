<?php

namespace Tests\Unit;

use DTApi\Helpers\TeHelper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function test_it_will_expired_at(): void
    {
        $dueTime = now()->addHours(10);
        $createdAt = now()->subHours(5);

        $result = TeHelper::willExpireAt($dueTime, $createdAt);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function test_it_will_expired_at_equal_to_due_time_when_different_less_than_90(): void
    {
        $dueTime = now()->addHours(50);
        $createdAt = now()->subHours(40);

        $result = TeHelper::willExpireAt($dueTime, $createdAt);

        $this->assertEquals($dueTime->format('Y-m-d H:i:s'), $result);
    }

    public function test_it_will_expired_at_equal_to_created_at_add_by_90_when_different_less_than_24(): void
    {
        $dueTime = now()->addHours(10);
        $createdAt = now()->subHours(14);

        $result = TeHelper::willExpireAt($dueTime, $createdAt);

        $expectedResult = $createdAt->addMinutes(90)->format('Y-m-d H:i:s');
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_will_expired_at_equal_to_created_at_add_by_16_when_different_between_24_and_72(): void
    {
        $dueTime = now()->addHours(40);
        $createdAt = now()->subHours(10);

        $result = TeHelper::willExpireAt($dueTime, $createdAt);

        $expectedResult = $createdAt->addHours(16)->format('Y-m-d H:i:s');

        $this->assertEquals($expectedResult, $result);
    }
}
