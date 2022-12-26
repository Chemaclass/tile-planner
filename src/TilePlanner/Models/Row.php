<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

final class Row implements \JsonSerializable
{
    private array $tiles = [];

    private float $width = 0;

    private float $currentLength = 0;

    private int $tileCounter = 0;

    public function addTile(Tile $tile): self
    {
        $this->tiles[] = $tile;

        $this->currentLength += $tile->getLength();
        ++$this->tileCounter;

        return $this;
    }

    public function getTiles(): array
    {
        return $this->tiles;
    }

    public function setWidth(float $width): void
    {
        $this->width = $width;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getCurrentRowLength(): float
    {
        return $this->currentLength;
    }

    public function getTileCount(): int
    {
        return $this->tileCounter;
    }

    public function jsonSerialize(): object
    {
        return (object) get_object_vars($this);
    }
}
