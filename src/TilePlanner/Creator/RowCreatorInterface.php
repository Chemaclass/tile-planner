<?php

namespace TilePlanner\TilePlanner\Creator;

use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

interface RowCreatorInterface
{
    public function createRow(
        TilePlanInput $tileInput,
        TilePlan $plan,
        Rests $rest
    ): Row;
}
