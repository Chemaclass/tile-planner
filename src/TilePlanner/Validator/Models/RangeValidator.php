<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Validator\Models;

use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

final class RangeValidator implements ValidatorInterface
{
    public function __construct(private TileLengthRangeCreatorInterface $rangeCreator) {
    }

    public function isValid(float $tileLength, TilePlanInput $tileInput, TilePlan $plan): bool
    {
        $tileRanges = $this->rangeCreator->calculateRanges($tileInput);

        foreach ($tileRanges->getRanges() as $range) {
            if ($range->inRange($tileLength)) {
                return true;
            }
        }

        return false;
    }
}
