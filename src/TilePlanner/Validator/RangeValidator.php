<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Validator;

use TilePlanner\TilePlanner\Models\LengthRange;

final class RangeValidator implements RangeValidatorInterface
{
    /**
     * @param  float             $length
     * @param  list<LengthRange> $firstTileRanges
     * @return bool
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
