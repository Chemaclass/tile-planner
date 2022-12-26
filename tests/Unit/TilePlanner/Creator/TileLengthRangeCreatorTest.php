<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator;

use TilePlanner\Form\TilePlannerType;
use TilePlanner\TilePlanner\Creator\TileLengthRangeCreator;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use PHPUnit\Framework\TestCase;

final class TileLengthRangeCreatorTest extends TestCase
{
    public function test_calculation_result_will_have_two_ranges(): void
    {
        $calculator = new TileLengthRangeCreator();
        $calculator::$rangeBag = null;

        $tileInput = new TilePlanInput(
            Room::create(200, 100),
            Tile::create(20, 50),
            (new LayingOptions())->setMinTileLength(20)
        );

        $actualRanges = $calculator->calculateRanges($tileInput);

        $this->assertCount(2, $actualRanges->getRanges());
        $this->assertEquals(20, $actualRanges->getMinOfFirstRange());
        $this->assertEquals(30, $actualRanges->getMaxOfFirstRange());
    }

    public function test_calculation_result_will_have_one_range(): void
    {
        $calculator = new TileLengthRangeCreator();
        $calculator::$rangeBag = null;

        $tileInput = new TilePlanInput(
            Room::create(200, 100),
            Tile::create(20, 50),
            (new LayingOptions())->setMinTileLength(30),
        );

        $actualRanges = $calculator->calculateRanges($tileInput);

        $this->assertCount(1, $actualRanges->getRanges());
        $this->assertEquals(50, $actualRanges->getMinOfFirstRange());
        $this->assertEquals(50, $actualRanges->getMaxOfFirstRange());
    }
}
