<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\LastTileCreator;

use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

interface LastTileCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, RestBag $rests, float $usedRowLength): ?Tile;
}
