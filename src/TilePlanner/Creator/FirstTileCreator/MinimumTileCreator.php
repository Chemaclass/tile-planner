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
use TilePlanner\TilePlanner\Validator\OffsetValidatorInterface;

final class MinimumTileCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private TileLengthRangeCreatorInterface $rangeCalculator,
        private OffsetValidatorInterface $offsetValidator
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $tileInput->getMinTileLength();
        $tileLength = $tileInput->getTileLength();

        $lengthTileLastRow = $plan->getLastRowLength();

        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);
        $minLengthOfFirstRange = $tileRanges->getMinOfFirstRange();

        if (
            $this->offsetValidator->isValidOffset(
                $minLengthOfFirstRange,
                $lengthTileLastRow,
                $tileMinLength,
                TilePlannerConstants::DEFAULT_MIN_OFFSET
            )
        ) {
            $tile = Tile::create(
                $tileInput->getTileWidth(),
                $minLengthOfFirstRange,
                TileCounter::next()
            );
            $rests->addRest(
                $tileLength - $minLengthOfFirstRange,
                $tileMinLength,
                TilePlannerConstants::RESTS_RIGHT,
                $tile->getNumber()
            );

            return $tile;
        }

        return null;
    }
}
