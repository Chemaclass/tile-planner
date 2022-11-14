<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Validator;

interface RangeValidatorInterface
{
    public function isInRange(float $length, array $firstTileRanges): bool;
}
