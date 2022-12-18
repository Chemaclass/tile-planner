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
use TilePlanner\TilePlanner\Validator\RangeValidatorInterface;

final class TileFromSmallestRestCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private TileLengthRangeCreatorInterface $rangeCalculator,
        private SmallestRestFinderInterface $smallestRestFinder,
        private RangeValidatorInterface $rangeValidator,
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileWidthWithDeviation = $this->calculateTileWithDeviation($plan, $tileInput, $rests);

        if ($tileWidthWithDeviation === null) {
            return null;
        }

        $smallestRest = $this
            ->smallestRestFinder
            ->findSmallestRestWithMinLength(
                TilePlannerConstants::RESTS_LEFT,
                $tileWidthWithDeviation
            );

        if ($smallestRest === null) {
            return null;
        }

        $rests->removeRest($smallestRest->getLength(), TilePlannerConstants::RESTS_LEFT);
        $trash = $smallestRest->getLength() - $tileWidthWithDeviation;
        $rests->addThrash($trash);

        return Tile::create(
            $tileInput->getTileWidth(),
            $tileWidthWithDeviation,
            $smallestRest->getNumber(),
        );
    }

    private function calculateTileWithDeviation(
        TilePlan $plan,
        TilePlanInput $tileInput,
        Rests $rests
    ): ?float {
        $lengthTileLastRow = $plan->getLastRowLength();
        $ranges = $this->rangeCalculator->calculateRanges($tileInput)->getRanges();

        $tileWidthWithDeviation = $lengthTileLastRow - TilePlannerConstants::MIN_DEVIATION;

        if (!$this->rangeValidator->isInRange($tileWidthWithDeviation, $ranges)) {
            return null;
        }

        $remainingRows = $tileInput->getTotalRows() - $plan->getRowsCount();

        if (count($rests->getRests(TilePlannerConstants::RESTS_LEFT)) < $remainingRows) {
            return null;
        }

        return $tileWidthWithDeviation;
    }
}
