<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\LastTileCreator;

use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;

interface LastTileCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests, float $usedRowLength): ?Tile;
}
