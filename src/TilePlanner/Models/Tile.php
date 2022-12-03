<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

final class Tile implements \JsonSerializable
{
    private function __construct(
        private float $width,
        private float $length,
        private ?int $number
    ) {
    }

    public static function create(float $width, float $length, ?int $number = null): self
    {
        return new self($width, $length, $number);
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function jsonSerialize(): object
    {
        return (object) get_object_vars($this);
    }
}
