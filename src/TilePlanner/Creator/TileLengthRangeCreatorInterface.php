<?php

namespace TilePlanner\TilePlanner\Creator;

use TilePlanner\TilePlanner\Models\LengthRangeBag;
use TilePlanner\TilePlanner\Models\TilePlanInput;

interface TileLengthRangeCreatorInterface
{
    public function calculateRanges(TilePlanInput $tileInput): LengthRangeBag;
}
