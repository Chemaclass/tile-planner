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
use TilePlanner\TilePlanner\Validator\RangeValidatorInterface;

final class FullTileCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private RangeValidatorInterface $rangeValidator,
        private OffsetValidatorInterface $offsetValidator,
        private TileLengthRangeCreatorInterface $rangeCalculator
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $tileInput->getMinTileLength();
        $tileLength = $tileInput->getTileLength();

        $lengthTileLastRow = $plan->getLastRowLength();
        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);

        if (
            $this->rangeValidator->isInRange($tileLength, $tileRanges->getRanges())
            && $this->offsetValidator->isValidOffset(
                $tileLength,
                $lengthTileLastRow,
                $tileMinLength,
                $tileInput->getLayingOptions()->getMinOffset()
            )
        ) {
            return Tile::create(
                $tileInput->getTileWidth(),
                $tileLength,
                TileCounter::next(),
            );
        }

        return null;
    }
}
