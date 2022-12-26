<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Creator;

use TilePlanner\TilePlanner\Models\Rests;
use TilePlanner\TilePlanner\Models\Row;
use TilePlanner\TilePlanner\Models\Tile;
use TilePlanner\TilePlanner\Models\TileCounter;
use TilePlanner\TilePlanner\Models\TilePlan;
use TilePlanner\TilePlanner\Models\TilePlanInput;

final class RowCreator implements RowCreatorInterface
{
    private Row $row;

    public function __construct(
        private FirstTileLengthCreatorInterface $firstTileLengthCreator,
        private LastTileLengthCreatorInterface $lastTileLengthCreator
    ) {
    }

    public function createRow(
        TilePlanInput $tileInput,
        TilePlan $plan,
        Rests $rest
    ): Row {
        $this->row = new Row();

        while (!$this->isRowEnd($tileInput->getRoomWidth())) {
            $tile = $this->calculateTile(
                $tileInput,
                $plan,
                $rest
            );

            $this->row->addTile($tile);
        }

        $rowWidth = $this->calculateRowWidth(
            $tileInput->getRoomDepth(),
            $tileInput->getTileWidth(),
            $plan->getRowsCount()
        );

        $this->row->setWidth($rowWidth);

        return $this->row;
    }

    private function isRowEnd(float $roomWidth): bool
    {
        return $this->row->getCurrentRowLength() >= $roomWidth;
    }

    private function calculateTile(
        TilePlanInput $tileInput,
        TilePlan $plan,
        Rests $rests): Tile
    {
        if ($this->isFirstTileOfRow()) {
            return $this->firstTileLengthCreator->create(
                $tileInput,
                $plan,
                $rests
            );
        }

        if ($this->isLastTileOfRow($tileInput)) {
            return $this->lastTileLengthCreator->create(
                $tileInput,
                $plan,
                $rests,
                $this->row->getCurrentRowLength()
            );
        }

        return $this->createTile(
            $tileInput->getTileWidth(),
            $tileInput->getTileLength()
        );
    }

    private function isLastTileOfRow(TilePlanInput $tileInput): bool
    {
        $restOfRow = $tileInput->getRoomWidth() - $this->row->getCurrentRowLength();

        return $restOfRow < $tileInput->getTileLength();
    }

    private function createTile(float $width, float $length): Tile
    {
        return Tile::create(
            $width,
            $length,
            TileCounter::next()
        );
    }

    private function calculateRowWidth(float $roomDepth, float $tileWidth, int $totalRows): float
    {
        $currentTiledRoomDepth = $totalRows * $tileWidth;

        if ($currentTiledRoomDepth + $tileWidth > $roomDepth) {
            return $roomDepth - $currentTiledRoomDepth;
        }

        return $tileWidth;
    }

    private function isFirstTileOfRow(): bool
    {
        return $this->row->getTileCount() === 0;
    }
}
