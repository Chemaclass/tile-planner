<?php

namespace TilePlanner\TilePlanner\Creator\Helper;

use TilePlanner\TilePlanner\Models\Rest;

interface SmallestRestFinderInterface
{
    /**
     * @param list<Rest> $rests
     */
    public function findSmallestRest(array $rests): ?Rest;
}
