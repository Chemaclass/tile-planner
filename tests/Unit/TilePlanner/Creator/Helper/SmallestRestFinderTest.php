<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator\Helper;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Creator\Helper\SmallestRestFinder;
use TilePlanner\TilePlanner\Models\Rest;

final class SmallestRestFinderTest extends TestCase
{
    public function test_return_null_if_rests_are_empty(): void
    {
        $finder = new SmallestRestFinder();

        $rests = [];

        $actual = $finder->findSmallestRest($rests);

        $this->assertNull($actual);
    }

    public function test_return_current_element_if_array_has_one_item(): void
    {
        $finder = new SmallestRestFinder();

        $rests = [Rest::create(100, 5)];

        $actual = $finder->findSmallestRest($rests);

        $this->assertEquals(5, $actual->getNumber());
        $this->assertEquals(100, $actual->getLength());
    }

    public function test_return_smallest_element_if_array_has_multiple_items(): void
    {
        $finder = new SmallestRestFinder();

        $rests = [
            Rest::create(200, 2),
            Rest::create(100, 5),
            Rest::create(500, 1),
        ];

        $actual = $finder->findSmallestRest($rests);

        $this->assertEquals(5, $actual->getNumber());
        $this->assertEquals(100, $actual->getLength());
    }
}