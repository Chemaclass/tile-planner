<?php

namespace TilePlanner\TilePlanner\Creator;

use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;

interface FirstTileLengthCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): Tile;
}
