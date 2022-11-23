<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit;

use TilePlanner\TilePlanner;
use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlanInput;

final class TilePlannerTest extends TestCase
{
    public function test_plan_creation(): void
    {
        $inputData = new TilePlanInput(
            Room::create(300, 230),
            Tile::create(20, 110),
            new LayingOptions(30)
        );

        $planner = TilePlanner::createPlan($inputData);

        $plan = $planner;

        self::assertEquals('69000', $plan->getTotalArea());
        self::assertEquals('230', $plan->getRoomDepth());
        self::assertEquals('300', $plan->getRoomWidth());
        self::assertCount(12, $plan->getRows());
    }
}