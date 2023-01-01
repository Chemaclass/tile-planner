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

final class ChessTileCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private TileValidatorInterface $tileValidator,
        private TileLengthRangeCreatorInterface $rangeCreator,
    ) {
    }

    public function create(
        TilePlanInput $tileInput,
        TilePlan $plan,
        RestBag $restBag
    ): ?Tile {
        $tileLength = $tileInput->getTileLength();
        $tileRanges = $this->rangeCreator->calculateRanges($tileInput);

        if ($this->tileValidator->isValid($tileInput->getTileLength(), $tileInput, $plan)) {
            return Tile::create(
                $tileInput->getTileWidth(),
                $tileLength,
                TileCounter::next()
            );
        }

        $tile = Tile::create(
            $tileInput->getTileWidth(),
            $tileRanges->getMinOfFirstRange(),
            TileCounter::next()
        );

        $restBag->addRest(
            $tileLength - $tileRanges->getMinOfFirstRange(),
            $tileInput->getMinTileLength(),
            TilePlannerConstants::RESTS_LEFT,
            $tile->getNumber()
        );

        return $tile;
    }
}
