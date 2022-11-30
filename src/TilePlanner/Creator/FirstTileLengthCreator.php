<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator;

use TilePlanner\TilePlanner\Creator\FirstTileCreator\FirstTileCreatorInterface;
use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;
use TilePlanner\TilePlanner\Models\LengthRangeBag;
use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Tile;

final class FirstTileLengthCreator implements FirstTileLengthCreatorInterface
{
    /**
     * @var list<FirstTileCreatorInterface>
     */
    private array $firstTileLengthCalculator;

    public function __construct(array $firstTileLengthCalculator)
    {
        $this->firstTileLengthCalculator = $firstTileLengthCalculator;
    }

    public function create(TilePlanInput $tileInput, TilePlan $plan, Rests $rests): Tile
    {
        $tileLength = $tileInput->getTileLength();

        foreach ($this->firstTileLengthCalculator as $calculator) {
            $tile = $calculator->create($tileInput, $plan, $rests);

            if ($tile !== null) {
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
