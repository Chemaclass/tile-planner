<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator\FirstTileCreator;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\TileFromMatchingRestCreator;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\TileFromSmallestRestCreator;
use TilePlanner\TilePlanner\Creator\Helper\SmallestRestFinderInterface;
use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\LengthRange;
use TilePlanner\TilePlanner\Models\LengthRangeBag;
use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\DeviationValidatorInterface;
use TilePlanner\TilePlanner\Validator\RangeValidatorInterface;

final class TileFromSmallestRestCreatorTest extends TestCase
{
    private TilePlanInput $tileInput;

    public function setUp(): void
    {
        $this->tileInput = new TilePlanInput(
            Room::create(200, 80),
            Tile::create(20, 50),
            new LayingOptions(0)
        );
    }

    public function test_return_null_if_no_smallest_rest_was_found(): void
    {
        $rangeCreator = $this->createStub(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );
        $smallestRestFinder = $this->createStub(SmallestRestFinderInterface::class);
        $smallestRestFinder->method('findSmallestRestWithMinLength')->willReturn(null);

        $rangeValidator = $this->createStub(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(true);

        $creator = new TileFromSmallestRestCreator(
            $rangeCreator,
            $smallestRestFinder,
            $rangeValidator
        );

        $plan = new TilePlan();
        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [
                    Rest::create(70, 3),
                ]
            ]
        );
        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertNull($actualTile);
    }

    public function test_return_null_if_rests_are_empty(): void
    {
        $rangeCreator = $this->createStub(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );
        $smallestRestFinder = $this->createStub(SmallestRestFinderInterface::class);
        $smallestRestFinder->method('findSmallestRestWithMinLength')->willReturn(Rest::create(50, 5));

        $rangeValidator = $this->createStub(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(true);

        $creator = new TileFromSmallestRestCreator(
            $rangeCreator,
            $smallestRestFinder,
            $rangeValidator,
        );

        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertNull($actualTile);
    }

    public function test_return_tile_cut_of_from_lowest_found_rest(): void
    {
        $rangeCreator = $this->createStub(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 50)))
        );

        $smallestRestFinder = $this->createStub(SmallestRestFinderInterface::class);
        $smallestRestFinder->method('findSmallestRestWithMinLength')->willReturn(Rest::create(50, 5));

        $rangeValidator = $this->createStub(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(true);

        $creator = new TileFromSmallestRestCreator(
            $rangeCreator,
            $smallestRestFinder,
            $rangeValidator,
        );

        $plan = new TilePlan();
        $plan->addRow((new Row())->addTile(Tile::create(20, 30)));

        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [
                    Rest::create(80, 1),
                    Rest::create(70, 3),
                    Rest::create(50, 5),
                ]
            ]
        );

        $expectedRest = [
            Rest::create(80, 1),
            Rest::create(70, 3),
        ];

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertEquals(20, $actualTile->getLength());
        self::assertEquals(5, $actualTile->getNumber());
        self::assertEquals($expectedRest, $rests->getRests(TilePlannerConstants::RESTS_LEFT));
    }

    public function test_return_null_if_smallest_rest_was_found(): void
    {
        $rangeValidator = $this->createMock(RangeValidatorInterface::class);
        $rangeValidator->method('isInRange')->willReturn(true);

        $deviationValidator = $this->createStub(DeviationValidatorInterface::class);
        $deviationValidator->method('isValidDeviation')->willReturn(true);

        $rangeCreator = $this->createStub(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new TileFromMatchingRestCreator($rangeValidator, $deviationValidator, $rangeCreator);

        $plan = new TilePlan();
        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [
                    Rest::create(30, 1),
                ]
            ]
        );

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertEquals(30, $actualTile->getLength());
        self::assertEmpty($rests->getRests(TilePlannerConstants::RESTS_LEFT));
    }
}
