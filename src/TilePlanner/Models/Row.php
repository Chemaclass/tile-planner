<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

final class Row implements \JsonSerializable
{
    private array $tiles = [];

    private float $width;

    public function addTile(Tile $tile): self
    {
        $this->tiles[] = $tile;

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

    public function jsonSerialize(): object
    {
        return (object) get_object_vars($this);
    }
}
