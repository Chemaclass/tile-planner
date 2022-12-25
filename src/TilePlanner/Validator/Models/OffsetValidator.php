<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Validator\Models;

use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

final class OffsetValidator implements ValidatorInterface
{
    public function isValid(float $tileLength, TilePlanInput $tileInput, TilePlan $plan): bool
    {
        $lastTileLength = $plan->getLastRowLength();
        $lastTileLengthMinusOffset = $lastTileLength - $tileInput->getLayingOptions()->getMinOffset();
        $lastTileLengthPlusOffset = $lastTileLength + $tileInput->getLayingOptions()->getMinOffset();

        if (empty($lastTileLength)) {
            return true;
        }

        if ($tileLength <= $lastTileLengthMinusOffset) {
            return true;
        }

        if ($tileLength >= $lastTileLengthPlusOffset) {
            return true;
        }

        return false;
    }
}
