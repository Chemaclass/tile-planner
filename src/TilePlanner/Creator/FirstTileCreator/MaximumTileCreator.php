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

final class MaximumTileCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private TileValidatorInterface $tileValidator,
        private TileLengthRangeCreatorInterface $rangeCreator,
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, RestBag $restBag): ?Tile
    {
        $tileMinLength = $tileInput->getMinTileLength();
        $tileLength = $tileInput->getTileLength();

        $tileRanges = $this->rangeCreator->calculateRanges($tileInput);
        $maxLengthOfFirstRange = $tileRanges->getMaxOfFirstRange();

        if ($this->tileValidator->isValid($maxLengthOfFirstRange, $tileInput, $plan)) {
            $tile = Tile::create(
                $tileInput->getTileWidth(),
                $maxLengthOfFirstRange,
                TileCounter::next()
            );

            $restOfTile = $tileLength - $maxLengthOfFirstRange;

            $restBag->addRest(
                $restOfTile,
                $tileMinLength,
                TilePlannerConstants::RESTS_RIGHT,
                $tile->getNumber()
            );

            return $tile;
        }

        return null;
    }
}
