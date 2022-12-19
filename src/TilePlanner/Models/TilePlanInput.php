<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

final class TilePlanInput
{
    public function __construct(
        private Room $room,
        private Tile $tile,
        private LayingOptions $layingOptions,
    ) {
    }

    public function getRoomWidth(): float
    {
        if ($this->layingOptions->getGapWidth() > 0) {
            return $this->room->getWidth() - ($this->layingOptions->getGapWidth() * $this->getTotalHorizontalGaps());
        }

        return $this->room->getWidth();
    }

    public function getMinTileLength(): float
    {
        return $this->layingOptions->getMinTileLength();
    }

    public function getRoomDepth(): float
    {
        if (null !== $this->layingOptions->getGapWidth() && $this->layingOptions->getGapWidth() > 0) {
            return $this->room->getDepth() - ($this->layingOptions->getGapWidth() * $this->getTotalVerticalGaps());
        }

        return $this->room->getDepth();
    }

    public function getTileWidth(): float
    {
        return $this->tile->getWidth();
    }

    public function getTileLength(): float
    {
        return $this->tile->getLength();
    }

    public function getLayingType(): string
    {
        return $this->layingOptions->getLayingType();
    }

    public function getRoomWidthWithGaps(): float
    {
        return $this->room->getWidth();
    }

    public function getRoomDepthWithGaps(): float
    {
        return $this->room->getDepth();
    }

    public function getCostsPerSquare(): float
    {
        return $this->layingOptions->getCostsPerSquare();
    }

    private function getTotalHorizontalGaps(): int
    {
        return (int) floor($this->room->getWidth() / $this->tile->getLength());
    }

    private function getTotalVerticalGaps(): int
    {
        return (int) floor($this->room->getDepth() / $this->tile->getWidth());
    }

    public function getTotalRows(): int
    {
        return (int) ceil($this->getRoomDepth() / $this->getTileWidth());
    }

    public function getLayingOptions(): LayingOptions
    {
        return $this->layingOptions;
    }
}
