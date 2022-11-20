<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Creator;

use TilePlanner\Form\TilePlannerType;
use TilePlanner\TilePlanner\Creator\FirstTileLengthCreatorInterface;
use TilePlanner\TilePlanner\Creator\LastTileLengthCreatorInterface;
use TilePlanner\TilePlanner\Creator\RowCreator;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use PHPUnit\Framework\TestCase;

final class RowCreatorTest extends TestCase
{
    public function test(): void
    {
        $firstTileLengthCalculator = $this->createMock(FirstTileLengthCreatorInterface::class);
        $firstTileLengthCalculator
            ->method('create')
            ->willReturn(Tile::create(15, 25));

        $lastTileLengthCalculator = $this->createMock(LastTileLengthCreatorInterface::class);
        $lastTileLengthCalculator
            ->method('create')
            ->willReturn(Tile::create(15, 25));

        $creator = new RowCreator($firstTileLengthCalculator, $lastTileLengthCalculator);

        $plan = new TilePlan();
        $rest = new Rests();

        $tileInput = new TilePlanInput(
            Room::create(200, 100),
            Tile::create(20, 50),
            new LayingOptions(0)
        );

        $actual = $creator->createRow($tileInput, $plan, $rest);

        self::assertCount(5, $actual->getTiles());
    }
}