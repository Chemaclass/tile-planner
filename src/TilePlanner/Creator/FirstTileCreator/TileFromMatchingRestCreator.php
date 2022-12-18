<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\OffsetValidatorInterface;
use TilePlanner\TilePlanner\Validator\RangeValidatorInterface;

final class TileFromMatchingRestCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private RangeValidatorInterface $rangeValidator,
        private OffsetValidatorInterface $offsetValidator,
        private TileLengthRangeCreatorInterface $rangeCalculator,
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        if (!$rests->hasRest(TilePlannerConstants::RESTS_LEFT)) {
            return null;
        }

        $matchingRest = $this->findMatchingRest(
            $rests,
            $plan,
            $tileInput
        );

        if ($matchingRest !== null) {
            $rests->removeRest($matchingRest->getLength(), TilePlannerConstants::RESTS_LEFT);

            return Tile::create(
                $tileInput->getTileWidth(),
                $matchingRest->getLength(),
                $matchingRest->getNumber()
            );
        }

        return null;
    }

    private function findMatchingRest(
        Rests $rests,
        TilePlan $plan,
        TilePlanInput $tileInput
    ): ?Rest {
        $lengthTileLastRow = $plan->getLastRowLength();
        $tileMinLength = $tileInput->getMinTileLength();
        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);

        foreach ($rests->getRests(TilePlannerConstants::RESTS_LEFT) as $rest) {
            $restLength = $rest->getLength();

            if ($restLength === $plan->getRowBeforeLastLength()) {
                continue;
            }

            if (!$this->rangeValidator->isInRange($restLength, $tileRanges->getRanges())) {
                continue;
            }

            if (
                $this->offsetValidator->isValidOffset(
                    $restLength,
                    $lengthTileLastRow,
                    $tileMinLength,
                    $tileInput->getLayingOptions()->getMinOffset(),
                )
            ) {
                return $rest;
            }
        }

        return null;
    }
}
