<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator\Helper;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Creator\Helper\SmallestRestFinder;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\TilePlannerConstants;

final class SmallestRestFinderTest extends TestCase
{
    public function test_return_null_if_rests_are_empty(): void
    {
        $rests = new Rests();
        $finder = new SmallestRestFinder($rests);

        $actual = $finder->findSmallestRestWithMinLength(TilePlannerConstants::RESTS_LEFT, 30.0);

        $this->assertNull($actual);
    }

    public function test_return_current_element_if_array_has_one_item(): void
    {
        $rests = new Rests();
        $rests->addRest(100, 30.0, TilePlannerConstants::RESTS_LEFT, 5);
        $finder = new SmallestRestFinder($rests);

        $actual = $finder->findSmallestRestWithMinLength(TilePlannerConstants::RESTS_LEFT, 30.0);

        $this->assertEquals(5, $actual->getNumber());
        $this->assertEquals(100, $actual->getLength());
    }

    public function test_return_smallest_element_if_array_has_multiple_items(): void
    {
        $rests = new Rests();
        $rests->addRest(200, 30.0, TilePlannerConstants::RESTS_LEFT, 2);
        $rests->addRest(100, 30.0, TilePlannerConstants::RESTS_LEFT, 5);
        $rests->addRest(500, 30.0, TilePlannerConstants::RESTS_LEFT, 1);

        $finder = new SmallestRestFinder($rests);

        $actual = $finder->findSmallestRestWithMinLength(TilePlannerConstants::RESTS_LEFT, 30.0);

        $this->assertEquals(5, $actual->getNumber());
        $this->assertEquals(100, $actual->getLength());
    }
}
