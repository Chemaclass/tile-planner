<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\Helper;

use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\Rests;

final class SmallestRestFinder implements SmallestRestFinderInterface
{
    public function __construct(
        private Rests $rests
    ) {
    }

    public function findSmallestRestWithMinLength(string $side, float $minWidth): ?Rest
    {
        $restsForSide = $this->rests->getRests($side);

        if (empty($restsForSide)) {
            return null;
        }

        usort($restsForSide, static fn (Rest $a, Rest $b) => $a->getLength() <=> $b->getLength());

        foreach ($restsForSide as $rest) {
            if ($rest->getLength() >= $minWidth) {
                return $rest;
            }
        }

        return null;
    }
}
