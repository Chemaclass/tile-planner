<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Validator;

use PHPUnit\Framework\TestCase;
use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\LayingOptions;
use TilePlanner\TilePlanner\Models\LengthRange;
use TilePlanner\TilePlanner\Models\LengthRangeBag;
use TilePlanner\TilePlanner\Models\Room;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Validator\Models\RangeValidator;

final class RangeValidatorTest extends TestCase
{
    public function test_range_not_valid_when_not_in_range(): void
    {
        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())->addRange(
                LengthRange::withMinAndMax(30, 80)
            )
        );

        $validator = new RangeValidator($rangeCreator);

        $currentLength = 90;
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

        self::assertFalse($actual);
    }

    public function test_range_valid_when_in_range(): void
    {
        $rangeCreator = $this->createMock(TileLengthRangeCreatorInterface::class);
        $rangeCreator->method('calculateRanges')->willReturn(
            (new LengthRangeBag())->addRange(
                LengthRange::withMinAndMax(30, 80)
            )
        );

        $validator = new RangeValidator($rangeCreator);

        $currentLength = 60;
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
}
