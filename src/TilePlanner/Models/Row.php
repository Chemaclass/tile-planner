<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

final class Row implements \JsonSerializable
{
    private static array $tiles = [];

    private static float $width = 0;

    private static float $currentLength = 0;

    private static int $tileCounter = 0;

    public function __construct(
    ) {
    }

    public function addTile(Tile $tile): self
    {
        self::$tiles[] = $tile;

        self::$currentLength += $tile->getLength();
        self::$tileCounter++;

        return $this;
    }

    public function getTiles(): array
    {
        return self::$tiles;
    }

    public function setWidth(float $width): void
    {
        self::$width = $width;
    }

    public function getWidth(): float
    {
        return self::$width;
    }

    public function getCurrentRowLength(): float
    {
        return self::$currentLength;
    }

    public function getTileCount(): int
    {
        return self::$tileCounter;
    }

    public function jsonSerialize(): object
    {
        return (object) get_object_vars($this);
    }
}
