<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Validator;

use TilePlanner\TilePlanner\Models\LengthRange;

final class RangeValidator implements RangeValidatorInterface
{
    /**
     * @param list<LengthRange> $firstTileRanges
     */
    public function isInRange(float $length, array $firstTileRanges): bool
    {
        foreach ($firstTileRanges as $range) {
            if ($range->inRange($length)) {
                return true;
            }
        }

        return false;
    }
}
