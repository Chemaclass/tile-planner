<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator;

use TilePlanner\TilePlanner\Creator\LastTileCreator\LastTileCreatorInterface;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

final class LastTileLengthCreator implements LastTileLengthCreatorInterface
{
    /**
     * @var list<LastTileCreatorInterface>
     */
    private array $lastTileLengthCalculator;

    public function __construct(array $lastTileLengthCalculator)
    {
        $this->lastTileLengthCalculator = $lastTileLengthCalculator;
    }

    public function create(
        TilePlanInput $tileInput,
        TilePlan $plan,
        Rests $rests,
        float $usedRowLength
    ): Tile {
        $tileLength = $tileInput->getTileLength();

        foreach ($this->lastTileLengthCalculator as $calculator) {
            $tile = $calculator->create($tileInput, $plan, $rests, $usedRowLength);

            if (null !== $tile) {
                return $tile;
            }
        }

        return Tile::create(
            $tileInput->getTileWidth(),
            $tileLength,
            TileCounter::next()
        );
    }
}
