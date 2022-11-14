<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner;

use TilePlanner\Form\TilePlannerType;
use TilePlanner\TilePlanner\Creator\RowCreatorInterface;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlanCreator;
use TilePlanner\TilePlanner\TilePlannerConstants;
use PHPUnit\Framework\TestCase;

final class TilePlanCreatorTest extends TestCase
{
    public function test_plan_created_successful(): void
    {
        $rowCreator = $this->createStub(RowCreatorInterface::class);
        $rowCreator->method('createRow')->willReturn(
            (new Row())
                ->addTile(Tile::create(25,100))
        );

        $rests = new Rests();
        $rests::setRest(
            [
                TilePlannerConstants::RESTS_LEFT => [],
                TilePlannerConstants::RESTS_RIGHT => [],
            ]
        );

        $creator = new TilePlanCreator($rowCreator, $rests);

        $tileInput = TilePlanInput::fromData(
            [
                'room_width' => '100',
                'room_depth' => '25',
                'tile_width' => '25',
                'tile_length' => '50',
                'min_tile_length' => '20',
                'gap_width' => '0',
                'laying_type' => TilePlannerType::TYPE_OFFSET,
                'costs_per_square' => '2',
            ]
        );

        $plan = $creator->create($tileInput);

        self::assertCount(1, $plan->getRows());
        self::assertEquals(2500, $plan->getTotalArea());
        self::assertEquals(0.25, $plan->getTotalAreaInSquareMeter());
        self::assertEquals(0.5, $plan->getTotalPrice());
    }
}