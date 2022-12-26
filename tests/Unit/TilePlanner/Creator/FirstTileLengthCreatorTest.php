<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator;

use TilePlanner\TilePlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use TilePlanner\TilePlanner\Creator\FirstTileLengthCreator;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use PHPUnit\Framework\TestCase;

final class FirstTileLengthCreatorTest extends TestCase
{
    private TilePlanInput $tileInput;

    public function setUp(): void
    {
        $tileOptions = (new LayingOptions())
            ->setMinTileLength(30)
            ->setGapWidth(5)
            ->setCostsPerSquare(20);

        $this->tileInput = new TilePlanInput(
            Room::create(400, 300),
            Tile::create(20, 100),
            $tileOptions,
        );
    }

    public function test_calculate_uses_defaults_without_any_calculator(): void
    {
        $calculator = new FirstTileLengthCreator([]);

        $rests = new Rests();
        $plan = new TilePlan();

        $actualTile = $calculator->create($this->tileInput, $plan, $rests);

        self::assertEquals(100, $actualTile->getLength());
        self::assertEquals(20, $actualTile->getWidth());
    }

    public function test_calculate_uses_returned_tile_from_calculator(): void
    {
        $calculatorStack = $this->createMock(FirstTileCreatorInterface::class);
        $calculatorStack->method('create')->willReturn(Tile::create(25, 120));

        $calculator = new FirstTileLengthCreator(
            [
            $calculatorStack
            ]
        );

        $rests = new Rests();
        $plan = new TilePlan();

        $actualTile = $calculator->create($this->tileInput, $plan, $rests);

        self::assertEquals(120, $actualTile->getLength());
        self::assertEquals(25, $actualTile->getWidth());
    }

    public function test_calculate_uses_defaults_when_returned_tile_from_calculator_is_null(): void
    {
        $calculatorStack = $this->createMock(FirstTileCreatorInterface::class);
        $calculatorStack->method('create')->willReturn(null);

        $calculator = new FirstTileLengthCreator(
            [
            $calculatorStack
            ]
        );

        $rests = new Rests();
        $plan = new TilePlan();

        $actualTile = $calculator->create($this->tileInput, $plan, $rests);

        self::assertEquals(100, $actualTile->getLength());
        self::assertEquals(20, $actualTile->getWidth());
    }
}
