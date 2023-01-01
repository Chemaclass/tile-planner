<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

interface FirstTileCreatorInterface
{
    public function create(
        TilePlanInput $tileInput,
        TilePlan $plan,
        RestBag $restBag
    ): ?Tile;
}
