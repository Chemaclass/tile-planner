<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Validator\TileValidatorInterface;

final class MinimumTileCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private TileValidatorInterface $tileValidator,
        private TileLengthRangeCreatorInterface $rangeCreator,
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, RestBag $restBag): ?Tile
    {
        $tileRanges = $this->rangeCreator->calculateRanges($tileInput);
        $minLengthOfFirstRange = $tileRanges->getMinOfFirstRange();

        if ($this->tileValidator->isValid($minLengthOfFirstRange, $tileInput, $plan)) {
            $tile = Tile::create(
                $tileInput->getTileWidth(),
                $minLengthOfFirstRange,
                TileCounter::next()
            );

            $rest = $tileInput->getTileLength() - $tileRanges->getMinOfFirstRange();

            $restBag->addRest(
                $rest,
                $tileInput->getMinTileLength(),
                TilePlannerConstants::RESTS_RIGHT,
                $tile->getNumber()
            );

            return $tile;
        }

        return null;
    }
}
