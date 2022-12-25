<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Validator;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Validator\Models\OffsetValidator;

final class OffsetValidatorTest extends TestCase
{
    public function test_offset_is_true_if_tile_in_first_row(): void
    {
        $validator = new OffsetValidator();

        $currentLength = 20;
        $plan = new TilePlan();
        $input = new TilePlanInput(
            Room::create(200, 300),
            Tile::create(20, 100),
            new LayingOptions(30)
        );

        $actual = $validator->isValid(
            $currentLength,
            $input,
            $plan,
        );

        self::assertTrue($actual);
    }

    public function test_offset_is_true_when_tile_smaller_last_tile_minus_offset(): void
    {
        $validator = new OffsetValidator();

        $currentLength = 60;
        $plan = new TilePlan();
        $plan->addRow((new Row())->addTile(Tile::create(20, 100)));

        $input = new TilePlanInput(
            Room::create(200, 300),
            Tile::create(20, 100),
            new LayingOptions(
                minTileLength: 30,
                minOffset: 30
            )
        );

        $actual = $validator->isValid(
            $currentLength,
            $input,
            $plan,
        );

        self::assertTrue($actual);
    }

    public function test_offset_is_true_when_tile_larger_last_tile_plus_offset(): void
    {
        $validator = new OffsetValidator();

        $currentLength = 100;
        $plan = new TilePlan();
        $plan->addRow((new Row())->addTile(Tile::create(20, 60)));

        $input = new TilePlanInput(
            Room::create(200, 300),
            Tile::create(20, 100),
            new LayingOptions(
                minTileLength: 30,
                minOffset: 30
            )
        );

        $actual = $validator->isValid(
            $currentLength,
            $input,
            $plan,
        );

        self::assertTrue($actual);
    }

    public function test_offset_is_false_when_tile_has_invalid_offset(): void
    {
        $validator = new OffsetValidator();

        $currentLength = 80;
        $plan = new TilePlan();
        $plan->addRow((new Row())->addTile(Tile::create(20, 60)));

        $input = new TilePlanInput(
            Room::create(200, 300),
            Tile::create(20, 100),
            new LayingOptions(
                minTileLength: 30,
                minOffset: 30
            )
        );

        $actual = $validator->isValid(
            $currentLength,
            $input,
            $plan,
        );

        self::assertFalse($actual);
    }
}
