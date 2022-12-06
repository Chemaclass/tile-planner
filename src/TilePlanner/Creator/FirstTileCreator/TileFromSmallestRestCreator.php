<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Creator\Helper\SmallestRestFinderInterface;
use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\DeviationValidatorInterface;

final class TileFromSmallestRestCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private DeviationValidatorInterface $deviationValidator,
        private TileLengthRangeCreatorInterface $rangeCalculator,
        private SmallestRestFinderInterface $smallestRestFinder,
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $tileInput->getMinTileLength();
        $lengthTileLastRow = $plan->getLastRowLength();
        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);

        if (!$rests->hasRest(TilePlannerConstants::RESTS_LEFT)) {
            return null;
        }

        $smallestRest = $this->smallestRestFinder
            ->findSmallestRest($rests->getRests(TilePlannerConstants::RESTS_LEFT));

        if ($smallestRest === null) {
            return null;
        }

        $maxLengthOfFirstRange = $tileRanges->getMaxOfFirstRange();

        if (
            $maxLengthOfFirstRange <= $smallestRest->getLength()
            && $this->deviationValidator->isValidDeviation(
                $maxLengthOfFirstRange,
                $lengthTileLastRow,
                $tileMinLength,
                TilePlannerConstants::MIN_DEVIATION
            )
        ) {
            $rests->removeRest($smallestRest->getLength(), TilePlannerConstants::RESTS_LEFT);

            $trash = $smallestRest->getLength() - $maxLengthOfFirstRange;

            $rests->addThrash($trash);

            return Tile::create(
                $tileInput->getTileWidth(),
                $maxLengthOfFirstRange,
                $smallestRest->getNumber(),
            );
        }

        return null;
    }
}
