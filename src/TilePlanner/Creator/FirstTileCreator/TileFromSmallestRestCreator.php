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
use TilePlanner\TilePlanner\Validator\RangeValidatorInterface;

final class TileFromSmallestRestCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private DeviationValidatorInterface $deviationValidator,
        private TileLengthRangeCreatorInterface $rangeCalculator,
        private SmallestRestFinderInterface $smallestRestFinder,
        private RangeValidatorInterface $rangeValidator,
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

        $tileWidthWithDeviation = $lengthTileLastRow - TilePlannerConstants::MIN_DEVIATION;

        if (!$this->rangeValidator->isInRange($tileWidthWithDeviation, $tileRanges->getRanges())) {
            return null;
        }

        if ($smallestRest->getLength() < $tileWidthWithDeviation) {
            return null;
        }

        if (count($rests->getRests(TilePlannerConstants::RESTS_LEFT)) < $tileInput->getTotalRows() - $plan->getRowsCount()) {
            return null;
        }

        if (
            $this->deviationValidator->isValidDeviation(
                $tileWidthWithDeviation,
                $lengthTileLastRow,
                $tileMinLength,
                TilePlannerConstants::MIN_DEVIATION
            )
        ) {
            $rests->removeRest($smallestRest->getLength(), TilePlannerConstants::RESTS_LEFT);

            $trash = $smallestRest->getLength() - $tileWidthWithDeviation;

            $rests->addThrash($trash);

            return Tile::create(
                $tileInput->getTileWidth(),
                $tileWidthWithDeviation,
                $smallestRest->getNumber(),
            );
        }

        return null;
    }
}
