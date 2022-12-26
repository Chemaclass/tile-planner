<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator;

use TilePlanner\Form\TilePlannerType;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use TilePlanner\TilePlanner\Creator\FirstTileLengthCreator;
use TilePlanner\TilePlanner\Creator\LastTileCreator\LastTileCreatorInterface;
use TilePlanner\TilePlanner\Creator\LastTileLengthCreator;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use PHPUnit\Framework\TestCase;

final class LastTileLengthCreatorTest extends TestCase
{
    private TilePlanInput $tileInput;

    public function setUp(): void
    {
        $this->tileInput = new TilePlanInput(
            Room::create(200, 100),
            Tile::create(20, 59),
            (new LayingOptions())->setMinTileLength(20)
        );
    }

    public function test_use_input_tile_length_if_no_creator_was_passed(): void
    {
        $creator = new LastTileLengthCreator([]);

        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->tileInput, $plan, $rests, 100);

        self::assertEquals(59, $actualTile->getLength());
    }

    public function test_use_returned_tile_length_from_creator(): void
    {
        $firstTileCreator = $this->createMock(LastTileCreatorInterface::class);
        $firstTileCreator
            ->method('create')
            ->willReturn(Tile::create(45, 65));

        $creator = new LastTileLengthCreator(
            [
            $firstTileCreator
            ]
        );

        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->tileInput, $plan, $rests, 100);

        self::assertEquals(65, $actualTile->getLength());
    }
}
