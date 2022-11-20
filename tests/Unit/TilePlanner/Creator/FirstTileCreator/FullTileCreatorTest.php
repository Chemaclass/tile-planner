<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator\FirstTileCreator;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\FullTileCreator;
use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\LengthRangeBag;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Validator\DeviationValidatorInterface;
use TilePlanner\TilePlanner\Validator\RangeValidatorInterface;

final class FullTileCreatorTest extends TestCase
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

    public function test_create_returns_tile_with_full_length(): void
    {
        $rangeValidator = $this->createMock(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(true);

        $deviationValidator = $this->createMock(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(true);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(new LengthRangeBag());

        $creator = new FullTileCreator($rangeValidator, $deviationValidator, $rangeCreator);

        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertEquals(50, $actualTile->getLength());
    }

    public function test_create_returns_null_when_deviation_is_not_valid(): void
    {
        $rangeValidator = $this->createStub(RangeValidatorInterface::class);

        $deviationValidator = $this->createMock(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(false);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(new LengthRangeBag());

        $creator = new FullTileCreator($rangeValidator, $deviationValidator, $rangeCreator);

        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertNull($actualTile);
    }

    public function test_create_returns_null_when_not_in_range(): void
    {
        $rangeValidator = $this->createMock(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(false);

        $deviationValidator = $this->createStub(DeviationValidatorInterface::class);

        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(new LengthRangeBag());

        $creator = new FullTileCreator($rangeValidator, $deviationValidator, $rangeCreator);

        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertNull($actualTile);
    }
}