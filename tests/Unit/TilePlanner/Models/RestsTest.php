<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Models;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\TilePlannerConstants;

final class RestsTest extends TestCase
{
    public function setUp(): void
    {
        $this->resetRests();
    }

    public function test_removing_one_rest_should_not_remove_all(): void
    {
        $rests = new Rests();

        $rests->addRest(90, 30, TilePlannerConstants::RESTS_LEFT, 1);
        $rests->addRest(90, 30, TilePlannerConstants::RESTS_LEFT, 2);

        $rests->removeRest(90, TilePlannerConstants::RESTS_LEFT);

        $remainingRests = $rests->getRests(TilePlannerConstants::RESTS_LEFT);

        $this->assertCount(1, $remainingRests);
        $this->assertEquals(90, current($remainingRests)->getLength());
    }

    public function test_sum_all_rests(): void
    {
        $rests = new Rests();

        $rests->addRest(40, 20, TilePlannerConstants::RESTS_LEFT, 1);
        $rests->addRest(30, 20, TilePlannerConstants::RESTS_LEFT, 2);
        $rests->addRest(20, 20, TilePlannerConstants::RESTS_RIGHT, 3);
        $rests->addThrash(10);

        $this->assertTrue($rests->hasRest(TilePlannerConstants::RESTS_LEFT));
        $this->assertTrue($rests->hasRest(TilePlannerConstants::RESTS_RIGHT));
        $this->assertEquals(100, $rests->totalLengthOfAllRests());
    }

    public function test_rest_is_trash_when_smaller_then_min_length(): void
    {
        $rests = new Rests();

        $rests->addRest(20, 30, TilePlannerConstants::RESTS_LEFT, 1);

        $this->assertFalse($rests->hasRest(TilePlannerConstants::RESTS_LEFT));
        $this->assertNotEmpty($rests->getTrash());
    }

    private function resetRests(): void
    {
        $reflection = new ReflectionClass(Rests::class);
        $reflection->setStaticPropertyValue('rest', [
            TilePlannerConstants::RESTS_LEFT => [],
            TilePlannerConstants::RESTS_RIGHT => []
        ]);
    }
}
