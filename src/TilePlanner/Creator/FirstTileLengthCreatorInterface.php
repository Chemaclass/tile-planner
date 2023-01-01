<?php

namespace TilePlanner\TilePlanner\Creator;

use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

interface FirstTileLengthCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, RestBag $rests): Tile;
}
