<?php

namespace TilePlanner\TilePlanner\Creator;

use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\LengthRangeBag;

interface TileLengthRangeCreatorInterface
{
    public function calculateRanges(TilePlanInput $tileInput): LengthRangeBag;
}
