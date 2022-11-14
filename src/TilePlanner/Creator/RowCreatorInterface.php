<?php

namespace TilePlanner\TilePlanner\Creator;

use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Row;

interface RowCreatorInterface
{
    public function createRow(
        TilePlanInput $tileInput,
        TilePlan      $plan,
        Rests         $rest
    ): Row;
}
