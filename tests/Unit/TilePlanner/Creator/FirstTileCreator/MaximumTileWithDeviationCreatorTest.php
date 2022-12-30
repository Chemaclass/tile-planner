<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator\FirstTileCreator;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\MaximumPossibleTileIncludingOffsetCreator;
use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\LengthRange;
use TilePlanner\TilePlanner\Models\LengthRangeBag;
use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\TileValidatorInterface;

final class MaximumTileWithDeviationCreatorTest extends TestCase
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
    public function test_return_null_when_it_is_first_row(): void
    {
        $tileValidator = $this->createStub(TileValidatorInterface::class);

        $rangeCalculator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCalculator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new MaximumPossibleTileIncludingOffsetCreator($tileValidator, $rangeCalculator);

        $plan = new TilePlan();
        $rests = new RestBag();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [
                    Rest::create(70, 3),
                ]
            ]
        );

        $tile = $creator->create($this->tileInput, $plan, $rests);

        $this->assertNull($tile);
    }

    public function test_return_null_tile_when_not_first_row_and_offset_is_not_valid(): void
    {
        $tileValidator = $this->createStub(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(false);

        $rangeCalculator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCalculator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new MaximumPossibleTileIncludingOffsetCreator($tileValidator, $rangeCalculator);

        $plan = new TilePlan();
        $plan->addRow((new Row())->addTile(Tile::create(20, 30)));

        $rests = new RestBag();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [
                    Rest::create(70, 3),
                ]
            ]
        );

        $tile = $creator->create($this->tileInput, $plan, $rests);

        $this->assertNull($tile);
    }

    public function test_return_valid_tile_when_not_first_row_and_offset_is_valid(): void
    {
        $tileValidator = $this->createStub(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(true);

        $rangeCalculator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCalculator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new MaximumPossibleTileIncludingOffsetCreator($tileValidator, $rangeCalculator);

        $plan = new TilePlan();
        $plan->addRow((new Row())->addTile(Tile::create(20, 30)));

        $rests = new RestBag();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [
                    Rest::create(70, 3),
                ]
            ]
        );

        $tile = $creator->create($this->tileInput, $plan, $rests);

        $this->assertNotNull($tile);
    }
}
