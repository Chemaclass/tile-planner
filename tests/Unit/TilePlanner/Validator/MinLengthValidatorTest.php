<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Validator;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Validator\Models\MinLengthValidator;
use TilePlanner\TilePlanner\Validator\Models\RangeValidator;

final class MinLengthValidatorTest extends TestCase
{
    public function test_valid_when_tile_greater_min_length(): void
    {
        $validator = new MinLengthValidator();

        $minLength = 30;
        $currentLength = 90;
        $plan = new TilePlan();

        $input = new TilePlanInput(
            Room::create(200, 300),
            Tile::create(20, 100),
            new LayingOptions($minLength)
        );

        $actual = $validator->isValid(
            $currentLength,
            $input,
            $plan,
        );

        self::assertTrue($actual);
    }

    public function test_not_valid_when_tile_lower_min_length(): void
    {
        $validator = new MinLengthValidator();

        $minLength = 30;
        $currentLength = 20;
        $plan = new TilePlan();

        $input = new TilePlanInput(
            Room::create(200, 300),
            Tile::create(20, 100),
            new LayingOptions($minLength)
        );

        $actual = $validator->isValid(
            $currentLength,
            $input,
            $plan,
        );

        self::assertFalse($actual);
    }
}
