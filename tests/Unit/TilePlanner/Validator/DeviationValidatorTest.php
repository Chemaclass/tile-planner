<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Validator;

use TilePlanner\TilePlanner\Validator\OffsetValidator;
use PHPUnit\Framework\TestCase;

final class DeviationValidatorTest extends TestCase
{
    public function test_deviation_is_false_without_last_length_and_current_length_smaller_min_length(): void
    {
        $validator = new OffsetValidator();

        $currentLength = 20;
        $lastLength = null;
        $tileMinLength = 30;
        $allowedDifference = 10;

        $actual = $validator->isValidOffset(
            $currentLength,
            $lastLength,
            $tileMinLength,
            $allowedDifference
        );

        self::assertFalse($actual);
    }

    public function test_deviation_is_false_when_length_lower_min_length(): void
    {
        $validator = new OffsetValidator();

        $currentLength = 20;
        $lastLength = 30;
        $tileMinLength = 30;
        $allowedDifference = 10;

        $actual = $validator->isValidOffset(
            $currentLength,
            $lastLength,
            $tileMinLength,
            $allowedDifference
        );

        self::assertFalse($actual);
    }

    public function test_deviation_is_false_when_deviation_to_small(): void
    {
        $validator = new OffsetValidator();

        $currentLength = 50;
        $lastLength = 45;
        $tileMinLength = 20;
        $allowedDifference = 10;

        $actual = $validator->isValidOffset(
            $currentLength,
            $lastLength,
            $tileMinLength,
            $allowedDifference
        );

        self::assertFalse($actual);
    }

    public function test_deviation__is_true_when_length_is_valid_and_deviation_is_valid(): void
    {
        $validator = new OffsetValidator();

        $currentLength = 50;
        $lastLength = 40;
        $tileMinLength = 20;
        $allowedDifference = 10;

        $actual = $validator->isValidOffset(
            $currentLength,
            $lastLength,
            $tileMinLength,
            $allowedDifference
        );

        self::assertTrue($actual);
    }
}
