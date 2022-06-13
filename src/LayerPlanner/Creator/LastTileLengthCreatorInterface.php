<?php

namespace App\LayerPlanner\Creator;

use App\LayerPlanner\Models\LayerPlan;
use App\LayerPlanner\Models\LayerPlanInput;
use App\LayerPlanner\Models\Rests;
use App\LayerPlanner\Models\Tile;

interface LastTileLengthCreatorInterface
{
    public function create(
        LayerPlanInput $layerInput,
        LayerPlan $plan,
        Rests $rests,
        float $usedRowLength
    ): Tile;
}
