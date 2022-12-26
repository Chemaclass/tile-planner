<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator\FirstTileCreator;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Creator\FirstTileCreator\FullTileCreator;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Validator\TileValidatorInterface;

final class FullTileCreatorTest extends TestCase
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

    public function test_create_returns_tile_with_full_length(): void
    {
        $tileValidator = $this->createMock(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(true);

        $creator = new FullTileCreator($tileValidator);

        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertEquals(50, $actualTile->getLength());
    }

    public function test_create_returns_null_when_length_not_valid(): void
    {
        $tileValidator = $this->createMock(TileValidatorInterface::class);
        $tileValidator->method('isValid')->willReturn(false);

        $creator = new FullTileCreator($tileValidator);

        $plan = new TilePlan();
        $rests = new Rests();

        $actualTile = $creator->create($this->tileInput, $plan, $rests);

        self::assertNull($actualTile);
    }
}
