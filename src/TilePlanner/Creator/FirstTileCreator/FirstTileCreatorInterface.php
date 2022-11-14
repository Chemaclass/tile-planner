<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;

interface FirstTileCreatorInterface
{
    public function create(
        TilePlanInput $tileInput,
        TilePlan      $plan,
        Rests         $rests
    ): ?Tile;
}
