<?php

namespace TilePlanner\TilePlanner;

use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

interface TilePlannerFacadeInterface
{
    public function createPlan(TilePlanInput $tileInput): TilePlan;
}