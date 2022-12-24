<?php

namespace TilePlanner\TilePlanner\Validator;

use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

interface TileValidatorInterface
{
    public function isValid(float $tileLength, TilePlanInput $tileInput, TilePlan $plan): bool;
}
