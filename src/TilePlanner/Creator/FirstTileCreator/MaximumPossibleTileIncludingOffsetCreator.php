<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\TileValidatorInterface;

final class MaximumPossibleTileIncludingOffsetCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private TileValidatorInterface $tileValidator,
        private TileLengthRangeCreatorInterface $rangeCalculator
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $tileInput->getMinTileLength();
        $tileLength = $tileInput->getTileLength();

        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);
        $maxLengthOfFirstRange = $tileRanges->getMaxOfFirstRange();

        $maxLengthOfFirstRangeWithOffset = $maxLengthOfFirstRange - $tileInput->getLayingOptions()->getMinOffset();

        if (0 === $plan->getRowsCount()) {
            return null;
        }

        if ($this->tileValidator->isValid($maxLengthOfFirstRangeWithOffset, $tileInput, $plan)) {
            $tile = Tile::create(
                $tileInput->getTileWidth(),
                $maxLengthOfFirstRangeWithOffset,
                TileCounter::next()
            );

            $restOfTile = $tileLength - $maxLengthOfFirstRangeWithOffset;

            $rests->addRest(
                $restOfTile,
                $tileMinLength,
                TilePlannerConstants::RESTS_RIGHT,
                $tile->getNumber()
            );

            return $tile;
        }

        return null;
    }
}
