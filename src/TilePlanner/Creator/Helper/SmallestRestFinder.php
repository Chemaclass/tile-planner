<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\Helper;

use TilePlanner\TilePlanner\Models\Rest;

final class SmallestRestFinder
{
    /**
     * @param list<Rest> $rests
     */
    public function findSmallestRest(array $rests): ?Rest
    {
        if (empty($rests)) {
            return null;
        }

        if (count($rests) === 1) {
            return current($rests);
        }

        usort($rests, static fn(Rest $a, Rest $b) => $a->getLength() <=> $b->getLength());

        return reset($rests);
    }
}
