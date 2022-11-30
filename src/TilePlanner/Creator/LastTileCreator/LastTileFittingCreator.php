<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\LastTileCreator;

use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\TilePlannerConstants;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;

final class LastTileFittingCreator implements LastTileCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests, float $usedRowLength): ?Tile
    {
        $restOfRow = $tileInput->getRoomWidth() - $usedRowLength;
        $restOfTile = $tileInput->getTileLength() - $restOfRow;

        $tile = Tile::create(
            $tileInput->getTileWidth(),
            $restOfRow,
            TileCounter::next()
        );

        $rests->addRest(
            $restOfTile,
            $tileInput->getMinTileLength(),
            TilePlannerConstants::RESTS_LEFT,
            $tile->getNumber()
        );

        return $tile;
    }
}
