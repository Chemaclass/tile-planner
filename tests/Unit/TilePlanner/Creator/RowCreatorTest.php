<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Creator\FirstTileLengthCreatorInterface;
use TilePlanner\TilePlanner\Creator\LastTileLengthCreatorInterface;
use TilePlanner\TilePlanner\Creator\RowCreator;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

final class RowCreatorTest extends TestCase
{
    private RowCreator $creator;

    protected function setUp(): void
    {
        $firstTileLengthCalculator = $this->createMock(FirstTileLengthCreatorInterface::class);
        $firstTileLengthCalculator
            ->method('create')
            ->willReturn(Tile::create(20, 120, 1));

        $lastTileLengthCalculator = $this->createMock(LastTileLengthCreatorInterface::class);
        $lastTileLengthCalculator
            ->method('create')
            ->willReturn(Tile::create(20, 120, 1));

        $this->creator = new RowCreator($firstTileLengthCalculator, $lastTileLengthCalculator);
    }

    public function test_row_has_correct_amount_of_tiles(): void
    {
        $plan = new TilePlan();
        $rest = new RestBag();

        $tileInput = new TilePlanInput(
            Room::create(200, 100),
            Tile::create(20, 120),
            (new LayingOptions())->setMinTileLength(0)
        );

        $actualRow = $this->creator->createRow($tileInput, $plan, $rest);

        self::assertCount(2, $actualRow->getTiles());
    }

    public function test_row_has_same_with_as_tile(): void
    {
        $plan = new TilePlan();
        $rest = new RestBag();

        $tileInput = new TilePlanInput(
            Room::create(200, 100),
            Tile::create(20, 50),
            new LayingOptions()
        );

        $actualRow = $this->creator->createRow($tileInput, $plan, $rest);

        $this->assertEquals(20, $actualRow->getWidth());
    }

    public function test_with_of_last_row_is_less_than_tile_width(): void
    {
        $plan = new TilePlan();
        $plan->addRow(new Row());

        $rest = new RestBag();

        $tileInput = new TilePlanInput(
            Room::create(200, 30),
            Tile::create(20, 50),
            new LayingOptions()
        );

        $actualRow = $this->creator->createRow($tileInput, $plan, $rest);

        $this->assertEquals(10, $actualRow->getWidth());
    }

    public function test_first_tile_of_row_has_number_one(): void
    {
        $plan = new TilePlan();
        $rest = new RestBag();

        $tileInput = new TilePlanInput(
            Room::create(450, 330),
            Tile::create(20, 120),
            (new LayingOptions())->setMinTileLength(30)
        );

        $actualRow = $this->creator->createRow($tileInput, $plan, $rest);

        $this->assertEquals(1, $actualRow->getTiles()[0]->getNumber());
    }
}
