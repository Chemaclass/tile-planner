<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\LengthRangeBag;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\OffsetValidatorInterface;

final class MaximumTileCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private OffsetValidatorInterface $offsetValidator,
        private TileLengthRangeCreatorInterface $rangeCalculator
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $tileInput->getMinTileLength();
        $tileLength = $tileInput->getTileLength();

        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);
        $maxLengthOfFirstRange = $tileRanges->getMaxOfFirstRange();

        if ($this->canUseMaxLengthOfFirstRange($plan, $tileInput, $tileRanges)) {
            $tile = Tile::create(
                $tileInput->getTileWidth(),
                $maxLengthOfFirstRange,
                TileCounter::next()
            );

            $restOfTile = $tileLength - $maxLengthOfFirstRange;

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

    private function canUseMaxLengthOfFirstRange(
        TilePlan $plan,
        TilePlanInput $tileInput,
        LengthRangeBag $tileRanges
    ): bool {
        if (
            $this->offsetValidator->isValidOffset(
                $tileRanges->getMaxOfFirstRange(),
                $plan->getLastRowLength(),
                $tileInput->getMinTileLength(),
                $tileInput->getLayingOptions()->getMinOffset()
            )
        ) {
            return true;
        }

        return false;
    }
}
