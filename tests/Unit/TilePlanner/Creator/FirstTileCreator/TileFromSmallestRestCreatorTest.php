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
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\TileValidatorInterface;

final class TileFromSmallestRestCreatorTest extends TestCase
{
    private TilePlanInput $tileInput;

    public function setUp(): void
    {
        $this->tileInput = new TilePlanInput(
            Room::create(200, 80),
            Tile::create(20, 50),
            new LayingOptions()
        );
    }

    public function test_return_null_if_no_smallest_rest_was_found(): void
    {
        $tileValidator = $this->createStub(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(true);

        $smallestRestFinder = $this->createStub(SmallestRestFinderInterface::class);
        $smallestRestFinder->method('findSmallestRestWithMinLength')->willReturn(null);

        $creator = new TileFromSmallestRestCreator(
            $tileValidator,
            $smallestRestFinder,
        );

        $plan = new TilePlan();
        $rests = new RestBag();
        $rests::setRest([
            Rest::createReusable(70, 3, TilePlannerConstants::RESTS_LEFT),
        ]);
        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertNull($actualTile);
    }

    public function test_return_null_if_rests_are_empty(): void
    {
        $tileValidator = $this->createStub(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(true);

        $smallestRestFinder = $this->createStub(SmallestRestFinderInterface::class);
        $smallestRestFinder->method('findSmallestRestWithMinLength')->willReturn(Rest::createReusable(50, 5, TilePlannerConstants::RESTS_LEFT));

        $creator = new TileFromSmallestRestCreator(
            $tileValidator,
            $smallestRestFinder,
        );

        $plan = new TilePlan();
        $rests = new RestBag();

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertNull($actualTile);
    }

    public function test_return_tile_cut_of_from_lowest_found_rest(): void
    {
        $tileValidator = $this->createStub(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(true);

        $smallestRestFinder = $this->createStub(SmallestRestFinderInterface::class);
        $smallestRestFinder->method('findSmallestRestWithMinLength')->willReturn(Rest::createReusable(50, 5, TilePlannerConstants::RESTS_LEFT));

        $creator = new TileFromSmallestRestCreator(
            $tileValidator,
            $smallestRestFinder,
        );

        $plan = new TilePlan();
        $plan->addRow((new Row())->addTile(Tile::create(20, 30)));

        $rests = new RestBag();
        $rests::setRest([
            Rest::createReusable(80, 1, TilePlannerConstants::RESTS_LEFT),
            Rest::createReusable(70, 3, TilePlannerConstants::RESTS_LEFT),
            Rest::createReusable(50, 5, TilePlannerConstants::RESTS_LEFT),
        ]);

        $expectedRest = [
            Rest::createReusable(80, 1, TilePlannerConstants::RESTS_LEFT),
            Rest::createReusable(70, 3, TilePlannerConstants::RESTS_LEFT),
        ];

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertEquals(20, $actualTile->getLength());
        self::assertEquals(5, $actualTile->getNumber());
        self::assertEquals($expectedRest, $rests->getReusableRestsForSide(TilePlannerConstants::RESTS_LEFT));
    }

    public function test_return_null_if_smallest_rest_was_found(): void
    {
        $tileValidator = $this->createStub(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(true);

        $rangeCreator = $this->createStub(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())
                ->addRange((LengthRange::withMinAndMax(10, 30)))
        );

        $creator = new TileFromMatchingRestCreator(
            $tileValidator

        );

        $plan = new TilePlan();
        $rests = new RestBag();
        $rests::setRest(
            [
                Rest::createReusable(30, 1, TilePlannerConstants::RESTS_LEFT),
            ]
        );

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertEquals(30, $actualTile->getLength());
        self::assertEmpty($rests->getReusableRestsForSide(TilePlannerConstants::RESTS_LEFT));
    }
}
