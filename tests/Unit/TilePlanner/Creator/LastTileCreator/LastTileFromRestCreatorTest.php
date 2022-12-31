<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator\LastTileCreator;

use ReflectionProperty;
use TilePlanner\Form\TilePlannerType;
use TilePlanner\TilePlanner\Creator\LastTileCreator\LastTileFromRestCreator;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\RestBag;
use PHPUnit\Framework\TestCase;

final class LastTileFromRestCreatorTest extends TestCase
{
    private TilePlanInput $tileInput;

    public function setUp(): void
    {
        $this->clearRests();

        $this->tileInput = new TilePlanInput(
            Room::create(200, 100),
            Tile::create(20, 50),
            new LayingOptions()
        );
    }

    public function test_return_null_when_no_rests_available(): void
    {
        $creator = new LastTileFromRestCreator();

        $plan = new TilePlan();
        $rests = new RestBag();

        $usedRowLength = 150;

        $actualTile = $creator->create($this->tileInput, $plan, $rests, $usedRowLength);

        self::assertNull($actualTile);
    }

    public function test_return_matching_tile_from_rest(): void
    {
        $creator = new LastTileFromRestCreator();

        $plan = new TilePlan();
        $rests = new RestBag();
        $rests->addRest(50, 20, TilePlannerConstants::RESTS_RIGHT, 1);

        $actualTile = $creator->create($this->tileInput, $plan, $rests, 150);

        self::assertEquals(50, $actualTile->getLength());
    }

    public function test_return_tile_cut_of_from_lowest_found_rest_having_one_rest(): void
    {
        $creator = new LastTileFromRestCreator();

        $plan = new TilePlan();
        $rests = new RestBag();
        $rests::setRest(
            [
                Rest::createReusable(80, 1, TilePlannerConstants::RESTS_RIGHT),
            ]
        );

        $actualTile = $creator->create($this->tileInput, $plan, $rests, 140);

        self::assertEquals(60, $actualTile->getLength());
        self::assertEmpty($rests->getReusableRestsForSide(TilePlannerConstants::RESTS_RIGHT));
    }

    public function test_return_tile_cut_of_from_lowest_found_rest_having_multiple_rests(): void
    {
        $creator = new LastTileFromRestCreator();

        $plan = new TilePlan();
        $rests = new RestBag();
        $rests->addRest(80, 10, TilePlannerConstants::RESTS_RIGHT, 1);
        $rests->addRest(70, 10, TilePlannerConstants::RESTS_RIGHT, 2);
        $rests->addRest(20, 10, TilePlannerConstants::RESTS_RIGHT, 3);

        $actualTile = $creator->create($this->tileInput, $plan, $rests, 140);

        $expectedRest = [
            Rest::createReusable(80, 1, TilePlannerConstants::RESTS_RIGHT),
            Rest::createReusable(20, 3, TilePlannerConstants::RESTS_RIGHT),
        ];

        self::assertEquals(60, $actualTile->getLength());
        self::assertEquals($expectedRest, array_values($rests->getReusableRestsForSide(TilePlannerConstants::RESTS_RIGHT)));
    }

    private function clearRests(): void
    {
        $restProperty = new ReflectionProperty(RestBag::class, 'rests');
        $restProperty->setAccessible(true);
        $restProperty->setValue([]);
    }
}
