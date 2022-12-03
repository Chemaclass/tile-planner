<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

use TilePlanner\Form\TilePlannerType;

final class LayingOptions
{
    public function __construct(
        private float $minTileLength,
        private float $costsPerSquare = 0,
        private string $layingType = TilePlannerType::TYPE_OFFSET,
        private ?float $gapWidth = null,
    ) {
    }

    public function getMinTileLength(): float
    {
        return $this->minTileLength;
    }

    public function getCostsPerSquare(): float
    {
        return $this->costsPerSquare;
    }

    public function getLayingType(): string
    {
        return $this->layingType;
    }

    public function getGapWidth(): ?float
    {
        return $this->gapWidth;
    }
}
