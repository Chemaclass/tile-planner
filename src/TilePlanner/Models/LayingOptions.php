<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

use TilePlanner\Form\TilePlannerType;
use TilePlanner\TilePlanner\TilePlannerConstants;

final class LayingOptions
{
    private float $minTileLength = TilePlannerConstants::DEFAULT_MIN_TILE_LENGTH;

    private float $costsPerSquare = 0;

    private string $layingType = TilePlannerType::TYPE_OFFSET;

    private ?float $gapWidth = null;

    private float $minOffset = TilePlannerConstants::DEFAULT_MIN_OFFSET;

    public function setMinTileLength(float $minTileLength): self
    {
        $this->minTileLength = $minTileLength;

        return $this;
    }

    public function setCostsPerSquare(float $costsPerSquare): self
    {
        $this->costsPerSquare = $costsPerSquare;

        return $this;
    }

    public function setLayingType(string $layingType): self
    {
        $this->layingType = $layingType;

        return $this;
    }

    public function setGapWidth(float $gapWidth): self
    {
        $this->gapWidth = $gapWidth;

        return $this;
    }

    public function setMinOffset(float $minOffset): self
    {
        $this->minOffset = $minOffset;

        return $this;
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
