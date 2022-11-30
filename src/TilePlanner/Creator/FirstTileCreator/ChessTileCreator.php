<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Creator\TileLengthRangeCreatorInterface;
use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Validator\RangeValidatorInterface;

final class ChessTileCreator implements FirstTileCreatorInterface
{
    private RangeValidatorInterface $rangeValidator;
    private TileLengthRangeCreatorInterface $rangeCreator;

    public function __construct(
        RangeValidatorInterface $rangeValidator,
        TileLengthRangeCreatorInterface $rangeCreator
    ) {
        $this->rangeValidator = $rangeValidator;
        $this->rangeCreator = $rangeCreator;
    }

    public function create(
        TilePlanInput $tileInput,
        TilePlan      $plan,
        Rests         $rests
    ): ?Tile {
        $tileLength = $tileInput->getTileLength();
        $tileRanges = $this->rangeCreator->calculateRanges($tileInput);

        if ($this->rangeValidator->isInRange($tileLength, $tileRanges->getRanges())) {
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

        $rests->addRest(
            $tileLength - $tileRanges->getMinOfFirstRange(),
            $tileInput->getMinTileLength(),
            TilePlannerConstants::RESTS_LEFT,
            $tile->getNumber()
        );

        return $tile;
    }
}
