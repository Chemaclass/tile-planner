<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Models;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\TilePlannerConstants;

final class RestBagTest extends TestCase
{
    public function test_removing_one_rest_should_not_remove_all(): void
    {
        $this->resetRests();

        $rests = new RestBag();

        $rests->addRest(90, 30, TilePlannerConstants::RESTS_LEFT, 1);
        $rests->addRest(90, 30, TilePlannerConstants::RESTS_LEFT, 2);

        $rests->removeRest(90, TilePlannerConstants::RESTS_LEFT);

        $remainingRests = $rests->getReusableRestsForSide(TilePlannerConstants::RESTS_LEFT);

        $this->assertCount(1, $remainingRests);
        $this->assertEquals(90, current($remainingRests)->getLength());
    }

    public function test_sum_all_rests(): void
    {
        $this->resetRests();

        $rests = new RestBag();

        $rests->addRest(40, 20, TilePlannerConstants::RESTS_LEFT, 1);
        $rests->addRest(30, 20, TilePlannerConstants::RESTS_LEFT, 2);
        $rests->addRest(20, 20, TilePlannerConstants::RESTS_RIGHT, 3);
        $rests->addNonReusableRest(10);

        $this->assertNotEmpty($rests->getReusableRestsForSide(TilePlannerConstants::RESTS_LEFT));
        $this->assertNotEmpty($rests->getReusableRestsForSide(TilePlannerConstants::RESTS_RIGHT));
        $this->assertEquals(100, $rests->totalLengthOfAllRests());
    }

    public function test_rest_is_trash_when_smaller_then_min_length(): void
    {
        $this->resetRests();

        $rests = new RestBag();
        $rests->addRest(20, 30, TilePlannerConstants::RESTS_LEFT, 1);

        $rest = Rest::createNonReusable(20, 1, TilePlannerConstants::RESTS_LEFT);

        $this->assertEmpty($rests->getReusableRestsForSide(TilePlannerConstants::RESTS_LEFT));
        $this->assertEquals([$rest], $rests->getNonReusableRests());
    }

    private function resetRests(): void
    {
        $reflection = new ReflectionClass(RestBag::class);
        $reflection->setStaticPropertyValue('rests', []);
    }
}
