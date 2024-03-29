<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Creator\Helper\SmallestRestFinderInterface;
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\TileValidatorInterface;

final class TileFromSmallestRestCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private TileValidatorInterface $tileValidator,
        private SmallestRestFinderInterface $smallestRestFinder,
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, RestBag $restBag): ?Tile
    {
        $tileWidthIncludingOffset = $this->calculateTileWithOffset($plan, $tileInput, $restBag);

        if (null === $tileWidthIncludingOffset) {
            return null;
        }

        $smallestRest = $this
            ->smallestRestFinder
            ->findSmallestRestWithMinLength(
                TilePlannerConstants::RESTS_LEFT,
                $tileWidthIncludingOffset
            )
        ;

        if (null === $smallestRest) {
            return null;
        }

        $restBag->removeRest($smallestRest->getLength(), TilePlannerConstants::RESTS_LEFT);
        $trash = $smallestRest->getLength() - $tileWidthIncludingOffset;
        $restBag->addNonReusableRest($trash);

        return Tile::create(
            $tileInput->getTileWidth(),
            $tileWidthIncludingOffset,
            $smallestRest->getNumber(),
        );
    }

    private function calculateTileWithOffset(
        TilePlan $plan,
        TilePlanInput $tileInput,
        RestBag $rests
    ): ?float {
        $lengthTileLastRow = $plan->getLastRowLength();
        $tileWidthIncludingOffset = $lengthTileLastRow - $tileInput->getLayingOptions()->getMinOffset();

        if (!$this->tileValidator->isValid($tileWidthIncludingOffset, $tileInput, $plan)) {
            return null;
        }

        $remainingRows = $tileInput->getTotalRows() - $plan->getRowsCount();

        if (\count($rests->getReusableRestsForSide(TilePlannerConstants::RESTS_LEFT)) < $remainingRows) {
            return null;
        }

        return $tileWidthIncludingOffset;
    }
}
