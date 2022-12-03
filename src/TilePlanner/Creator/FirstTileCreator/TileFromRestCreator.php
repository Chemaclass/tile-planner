<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\Rest;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\DeviationValidatorInterface;
use TilePlanner\TilePlanner\Validator\RangeValidatorInterface;

final class TileFromRestCreator implements FirstTileCreatorInterface
{
    private RangeValidatorInterface $rangeValidator;
    private DeviationValidatorInterface $deviationValidator;
    private TileLengthRangeCreatorInterface $rangeCalculator;

    public function __construct(
        RangeValidatorInterface $rangeValidator,
        DeviationValidatorInterface $deviationValidator,
        TileLengthRangeCreatorInterface $rangeCalculator
    ) {
        $this->rangeValidator = $rangeValidator;
        $this->deviationValidator = $deviationValidator;
        $this->rangeCalculator = $rangeCalculator;
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileMinLength = $tileInput->getMinTileLength();

        $lengthTileLastRow = $plan->getLastRowLength();
        $lengthTileBeforeLastRow = $plan->getRowBeforeLastLength();

        $tileRanges = $this->rangeCalculator->calculateRanges($tileInput);
        $maxLengthOfFirstRange = $tileRanges->getMaxOfFirstRange();

        if ($rests->hasRest(TilePlannerConstants::RESTS_LEFT)) {
            foreach ($rests->getRests(TilePlannerConstants::RESTS_LEFT) as $rest) {
                $restLength = $rest->getLength();

                if (
                    $restLength !== $lengthTileBeforeLastRow
                    && $this->deviationValidator->isValidDeviation(
                        $restLength,
                        $lengthTileLastRow,
                        $tileMinLength,
                        TilePlannerConstants::MIN_DEVIATION
                    )
                    && $this->rangeValidator->isInRange($restLength, $tileRanges->getRanges())
                ) {
                    $rests->removeRest($restLength, TilePlannerConstants::RESTS_LEFT);

                    return Tile::create(
                        $tileInput->getTileWidth(),
                        $restLength,
                        $rest->getNumber()
                    );
                }
            }

            $smallestRest = $this->getRestWithSmallestLength($rests->getRests(TilePlannerConstants::RESTS_LEFT));
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
                    TileCounter::next(),
                );
            }
        }

        return null;
    }

    /**
     * @param list<Rest> $possibleRests
     */
    private function getRestWithSmallestLength(array $possibleRests): Rest
    {
        if (1 === \count($possibleRests)) {
            return array_pop($possibleRests);
        }

        usort($possibleRests, static fn (Rest $a, Rest $b) => $a->getLength() <=> $b->getLength());

        return $possibleRests[0];
    }
}
