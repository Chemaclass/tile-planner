<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator\LastTileCreator;

use TilePlanner\Form\TilePlannerType;
use TilePlanner\TilePlanner\Creator\LastTileCreator\LastTileFromRestForChessTypeCreator;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\RestBag;
use PHPUnit\Framework\TestCase;

final class LastTileFromRestForChessTypeCreatorTest extends TestCase
{
    private TilePlanInput $tileInput;

    public function setUp(): void
    {
        $this->tileInput = new TilePlanInput(
            Room::create(200, 100),
            Tile::create(20, 50),
            new LayingOptions()
        );
    }

    public function test_return_null_when_no_rests(): void
    {
        $creator = new LastTileFromRestForChessTypeCreator();

        $plan = new TilePlan();
        $rests = new RestBag();

        $actualTile = $creator->create($this->tileInput, $plan, $rests, 150);

        self::assertNull($actualTile);
    }

    public function test_return_matching_tile_from_rest(): void
    {
        $creator = new LastTileFromRestForChessTypeCreator();

        $plan = new TilePlan();
        $rests = new RestBag();
        $rests::setRest(
            [
                Rest::createReusable(25, 1, TilePlannerConstants::RESTS_LEFT),
            ]
        );

        $actualTile = $creator->create($this->tileInput, $plan, $rests, 175);

        self::assertEquals(25, $actualTile->getLength());
    }

    public function test_return_tile_cut_of_from_lowest_found_rest_having_one_rest(): void
    {
        $creator = new LastTileFromRestForChessTypeCreator();

        $plan = new TilePlan();
        $rests = new RestBag();
        $rests::setRest(
            [
                Rest::createReusable(80, 1, TilePlannerConstants::RESTS_LEFT),
            ]
        );
        $usedRowLength = 175;

        $actualTile = $creator->create($this->tileInput, $plan, $rests, $usedRowLength);

        self::assertEquals(25, $actualTile->getLength());
        self::assertCount(0, $rests->getReusableRestsForSide(TilePlannerConstants::RESTS_LEFT));
    }

    public function test_return_tile_cut_of_from_lowest_found_rest_having_multiple_rests(): void
    {
        $creator = new LastTileFromRestForChessTypeCreator();

        $plan = new TilePlan();
        $rests = new RestBag();
        $rests::setRest(
            [
                Rest::createReusable(80, 1, TilePlannerConstants::RESTS_LEFT),
                Rest::createReusable(70, 2, TilePlannerConstants::RESTS_LEFT),
                Rest::createReusable(50, 3, TilePlannerConstants::RESTS_LEFT),
            ]
        );
        $usedRowLength = 175;

        $actualTile = $creator->create($this->tileInput, $plan, $rests, $usedRowLength);

        self::assertEquals(25, $actualTile->getLength());
        self::assertCount(2, $rests->getReusableRestsForSide(TilePlannerConstants::RESTS_LEFT));
    }
}
