<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\LastTileCreator;

use TilePlanner\TilePlanner\Models\RestBag;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\TilePlannerConstants;

final class LastTileFittingCreator implements LastTileCreatorInterface
{
    public function create(TilePlanInput $tileInput, TilePlan $plan, RestBag $rests, float $usedRowLength): ?Tile
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
