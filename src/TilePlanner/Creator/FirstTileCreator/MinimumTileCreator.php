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
use TilePlanner\TilePlanner\Validator\DeviationValidatorInterface;

final class MinimumTileCreator implements FirstTileCreatorInterface
{
    private TileLengthRangeCreatorInterface $rangeCalculator;

    private DeviationValidatorInterface $deviationValidator;

    public function __construct(
        TileLengthRangeCreatorInterface $rangeCalculator,
        DeviationValidatorInterface $deviationValidator
    ) {
        $this->rangeCalculator = $rangeCalculator;
        $this->deviationValidator = $deviationValidator;
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $tileInput->getMinTileLength();
        $tileLength = $tileInput->getTileLength();

        $lengthTileLastRow = $plan->getLastRowLength();

        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);
        $minLengthOfFirstRange = $tileRanges->getMinOfFirstRange();

        if (
            $this->deviationValidator->isValidDeviation(
                $minLengthOfFirstRange,
                $lengthTileLastRow,
                $tileMinLength,
                TilePlannerConstants::MIN_DEVIATION
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
