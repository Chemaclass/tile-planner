<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator\FirstTileCreator;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\TileFromMatchingRestCreator;
use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\LengthRange;
use TilePlanner\TilePlanner\Models\LengthRangeBag;
use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\TileValidatorInterface;

final class TileFromMatchingRestCreatorTest extends TestCase
{
    private TilePlanInput $tileInput;

    public function setUp(): void
    {
        $this->tileInput = new TilePlanInput(
            Room::create(200, 100),
            Tile::create(20, 50),
            new LayingOptions(0)
        );
    }

    public function test_return_null_if_has_no_rests_for_left_side(): void
    {
        $tileValidator = $this->createMock(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(false);

        $creator = new TileFromMatchingRestCreator($tileValidator);

        $plan = new TilePlan();
        $rests = new Rests();
        $rests::setRest([]);

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertNull($actualTile);
    }

    public function test_return_first_found_and_matching_tile_from_rest(): void
    {
        $tileValidator = $this->createMock(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(true);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new TileFromMatchingRestCreator($tileValidator);

        $plan = new TilePlan();
        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [
                    Rest::create(35, 1)
                ]
            ]
        );

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertEquals(35, $actualTile->getLength());
        self::assertEquals(1, $actualTile->getNumber());
    }
}
