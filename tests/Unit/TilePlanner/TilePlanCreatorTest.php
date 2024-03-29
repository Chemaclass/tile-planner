<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner;

use TilePlanner\Form\TilePlannerType;
use TilePlanner\TilePlanner\Creator\RowCreatorInterface;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlanCreator;
use TilePlanner\TilePlanner\TilePlannerConstants;
use PHPUnit\Framework\TestCase;

final class TilePlanCreatorTest extends TestCase
{
    public function test_plan_created_successful(): void
    {
        $rowCreator = $this->createStub(RowCreatorInterface::class);
        $rowCreator->method('createRow')->willReturn(
            (new Row())
                ->addTile(Tile::create(25, 100, 10))
        );

        $rests = new RestBag();

        $creator = new TilePlanCreator($rowCreator, $rests);

        $tileInput = new TilePlanInput(
            Room::create(100, 90),
            Tile::create(25, 50),
            (new LayingOptions())
                ->setMinTileLength(20)
                ->setCostsPerSquare(2)
        );

        $plan = $creator->create($tileInput);

        self::assertCount(4, $plan->getRows());
        self::assertEquals(9000, $plan->getTotalArea());
        self::assertEquals(0.9, $plan->getTotalAreaInSquareMeter());
        self::assertEquals(1.8, $plan->getTotalPrice());
    }
}
