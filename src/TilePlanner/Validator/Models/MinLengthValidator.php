<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Validator\Models;

use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

final class MinLengthValidator implements ValidatorInterface
{
    public function isValid(float $tileLength, TilePlanInput $tileInput, TilePlan $plan): bool
    {
        if ($tileLength < $tileInput->getMinTileLength()) {
            return false;
        }

        return true;
    }
}
