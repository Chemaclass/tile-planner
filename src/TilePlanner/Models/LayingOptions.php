<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

use TilePlanner\Form\TilePlannerType;
use TilePlanner\TilePlanner\TilePlannerConstants;

final class LayingOptions
{
    public function __construct(
        private float $minTileLength,
        private float $costsPerSquare = 0,
        private string $layingType = TilePlannerType::TYPE_OFFSET,
        private ?float $gapWidth = null,
        private float $minOffset = TilePlannerConstants::DEFAULT_MIN_OFFSET,
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

    public function getMinOffset(): float
    {
        return $this->minOffset;
    }
}
