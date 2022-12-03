<?php

declare(strict_types=1);

namespace TilePlanner\TilePlanner\Models;

final class Rest
{
    private float $length;
    private int $number;

    private function __construct(float $length, int $number)
    {
        $this->length = $length;
        $this->number = $number;
    }

    public static function create(float $length, int $number): self
    {
        return new self($length, $number);
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setLength(float $length): self
    {
        $this->length = $length;

        return $this;
    }
}
