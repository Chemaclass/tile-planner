<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\LengthRangeBag;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Validator\DeviationValidatorInterface;

final class MaximumTileCreator implements FirstTileCreatorInterface
{
    private DeviationValidatorInterface $deviationValidator;
    private TileLengthRangeCreatorInterface $rangeCalculator;

    public function __construct(
        DeviationValidatorInterface $deviationValidator,
        TileLengthRangeCreatorInterface $rangeCalculator
    ) {
        $this->deviationValidator = $deviationValidator;
        $this->rangeCalculator = $rangeCalculator;
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
            $this->deviationValidator->isValidDeviation(
                $tileRanges->getMaxOfFirstRange(),
                $plan->getLastRowLength(),
                $tileInput->getMinTileLength(),
                TilePlannerConstants::MIN_DEVIATION
            )
        ) {
            return true;
        }

        return false;
    }
}
