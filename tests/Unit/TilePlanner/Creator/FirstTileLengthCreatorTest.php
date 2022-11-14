<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator;

use TilePlanner\TilePlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use TilePlanner\TilePlanner\Creator\FirstTileLengthCreator;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use PHPUnit\Framework\TestCase;

final class FirstTileLengthCreatorTest extends TestCase
{
    private TilePlanInput $tileInput;

    private const PLAN_INPUT_DATA = [
        'room_width' => '400',
        'room_depth' => '300',
        'tile_width' => '20',
        'tile_length' => '100',
        'min_tile_length' => '30',
        'gap_width' => '5',
        'laying_type' => 'offset',
        'costs_per_square' => '20',
    ];

    public function setUp(): void
    {
        $this->tileInput = TilePlanInput::fromData(self::PLAN_INPUT_DATA);
    }

    public function test_calculate_uses_defaults_without_any_calculator(): void
    {
        $calculator = new FirstTileLengthCreator([]);

        $rests = new Rests();
        $plan = new TilePlan();

        $actualTile = $calculator->create($this->tileInput, $plan, $rests);

        self::assertEquals(self::PLAN_INPUT_DATA['tile_length'], $actualTile->getLength());
        self::assertEquals(self::PLAN_INPUT_DATA['tile_width'], $actualTile->getWidth());
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

        self::assertEquals(self::PLAN_INPUT_DATA['tile_length'], $actualTile->getLength());
        self::assertEquals(self::PLAN_INPUT_DATA['tile_width'], $actualTile->getWidth());
    }
}