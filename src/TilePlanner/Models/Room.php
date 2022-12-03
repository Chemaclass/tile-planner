<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

final class Room
{
    private const ROUND_PRECISION = 2;

    private function __construct(
        private float $width,
        private float $depth
    ) {
    }

    public static function create(float $width, float $depth): self
    {
        return new self($width, $depth);
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getDepth(): float
    {
        return $this->depth;
    }

    public function getSize(): float
    {
        return round($this->depth * $this->width, self::ROUND_PRECISION);
    }
}
