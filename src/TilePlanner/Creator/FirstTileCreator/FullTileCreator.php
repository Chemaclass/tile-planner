<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator\FirstTileCreator;

use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Validator\TileValidatorInterface;

final class FullTileCreator implements FirstTileCreatorInterface
{
    public function __construct(
        private TileValidatorInterface $tileValidator,
    ) {
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): ?Tile
    {
        $tileLength = $tileInput->getTileLength();

        if ($this->tileValidator->isValid($tileLength, $tileInput, $plan)) {
            return Tile::create(
                $tileInput->getTileWidth(),
                $tileLength,
                TileCounter::next(),
            );
        }

        return null;
    }
}
