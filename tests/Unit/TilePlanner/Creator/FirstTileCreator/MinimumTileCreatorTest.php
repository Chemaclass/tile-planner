<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator\FirstTileCreator;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\MinimumTileCreator;
use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\LengthRange;
use TilePlanner\TilePlanner\Models\LengthRangeBag;
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Validator\TileValidatorInterface;

final class MinimumTileCreatorTest extends TestCase
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

    public function test_create_return_null_when_deviation_is_false(): void
    {
        $rangeCalculator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCalculator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $tileValidator = $this->createStub(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(false);

        $creator = new MinimumTileCreator($tileValidator, $rangeCalculator);

        $plan = new TilePlan();
        $plan->addRow((new Row())->addTile(Tile::create(20, 30)));
        $rests = new RestBag();

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertNull($actualTile);
    }

    public function test_create_return_tile_with_min_length_if_first_of_last_row_has_max_length(): void
    {
        $rangeCalculator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCalculator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );
        $tileValidator = $this->createMock(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(true);

        $creator = new MinimumTileCreator($tileValidator, $rangeCalculator);

        $plan = new TilePlan();
        $plan->addRow((new Row())->addTile(Tile::create(20, 30)));
        $rests = new RestBag();

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertEquals(10, $actualTile->getLength());
    }
}
