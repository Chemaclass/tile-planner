<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator\LastTileCreator;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TilePlanner\TilePlanner\Creator\LastTileCreator\LastTileFittingCreator;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;

final class LastTileFittingCreatorTest extends TestCase
{
    public function test_return_fitting_tile(): void
    {
        $creator = new LastTileFittingCreator();

        $tileInput = new TilePlanInput(
            Room::create(200, 100),
            Tile::create(20, 50),
            new LayingOptions()
        );

        $plan = new TilePlan();
        $rests = new RestBag();

        $this->clearRests();

        $usedRowLength = 175;

        $actualTile = $creator->create($tileInput, $plan, $rests, $usedRowLength);

        self::assertEquals(25, $actualTile->getLength());
        self::assertCount(1, $rests->getReusableRestsForSide(TilePlannerConstants::RESTS_LEFT));
    }

    private function clearRests(): void
    {
        $restProperty = new ReflectionProperty(RestBag::class, 'rests');
        $restProperty->setAccessible(true);
        $restProperty->setValue([]);
    }
}
